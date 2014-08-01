<?php

define('FLEXGET_CONFIG_PATH', '/home/user/.flexget/config.yml');

$file = file_get_contents('database.php.txt');

if ($file) {
    $seriess = json_decode($file, true);
} else {
    $seriess = array();
}

function save_file($seriess) {
    file_put_contents('database.php.txt', json_encode($seriess));

    // Load flexget config
    $config = file_get_contents(FLEXGET_CONFIG_PATH);

    // Save backup
    file_put_contents(FLEXGET_CONFIG_PATH.'.backup', $config);

    // Add new items
    $new_items = "# START SERIES LIST\n      - ".implode("\n      - ", $seriess)."\n# END SERIES LIST";
    $config = preg_replace('/# START SERIES LIST(.*)# END SERIES LIST/s', $new_items, $config);

    // Save for realisies
    file_put_contents(FLEXGET_CONFIG_PATH, $config);
}

// Is this an action?
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['new'])) {
        $new = trim($_POST['new']);

        if (!in_array($new, $seriess)) {
            $seriess[] = $new;
        }

        $location = array_search($new, $seriess);

        save_file($seriess);

        echo $location;
    }

    if (isset($_POST['del'])) {
        $del = trim($_POST['del']);

        if (isset($seriess[$del])) {
            unset($seriess[$del]);
        }

        save_file($seriess);
    }

    die();
}

?>
<html>
<head>
<title>Flexget - series to download</title>
<script type="text/javascript" src="jquery.js"></script>

<style>

* {
    font-family: "Verdana";
}

h1 img {
    margin-left: 20px;
}

div.add_new_series {
    border: 2px solid #ccc;
    background-color: #eee;
    padding: 10px;
    margin: 5px 5px 10px;
}

div.add_new_series input {
    padding: 5px;
}

div.add_new_series input.input {
    width: 400px;
}

.series div {
    padding: 5px;
    border-top: 1px solid #ccc;
    float: left;
    width: 30%;
    margin-right: 2%;
}

.delete_series {
    margin-right: 20px;
}

</style>

<script>

$(function() {

    function loading_on() {
        var img = $('<img src="ajax-loader.gif" />');
        $('h1').append(img);
    }

    function loading_off() {
        $('h1 img').remove();
    }

    $(document).on('click', '.series input.delete_series', function() {
        var par = $(this).parent();

        loading_on();
        $.post(
            '/utils/flexget/',
            {
                'del': $(this).data('value')
            },
            function(result) {

                // Delete item
                par.remove();

                loading_off();
            }
        );
    });

    $('.add_new_series input.submit').click(function() {
        var par = $('.add_new_series');
        var input = $('input.input', par);
        if (!input.val()) {
            return;
        }

        loading_on();
        $.post(
            '/utils/flexget/',
            {
                'new': input.val()
            },
            function(result) {

                if (result) {
                    // Append result to list
                    var item = $(
                        '<div>'+
                        '<input type="button" class="delete_series" data-value="'+result+'" value="X" />'+
                        input.val()+
                        '</div>'
                    );
                    $('.series').prepend(item);
                }

                input.val('').focus();
                loading_off();
            }
        );
    });

    $('.add_new_series input.input').focus();

});

</script>

</head>
<body>

<h1>Flexget - series to download</h1>


<div class="add_new_series">
<input type="text" class="input" value="" />
<input type="submit" class="submit" value="Add series" />
</div>

<div class="series">
<?php

asort($seriess);
foreach ($seriess as $key => $series) {

?>
<div>
<input type="button" class="delete_series" data-value="<?php echo $key; ?>" value="X" />
<?php echo htmlentities(trim($series)); ?>
</div>
<?php
}
?>

</div>

</body>
</html>
