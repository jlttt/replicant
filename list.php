<?php
session_start();
require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/functions.php');
$_SESSION['replicant.configurations'] = [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Replicant One - List</title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap-theme.min.css"/>
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <script type="text/javascript" src="jquery/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script>
        $(document).on('input', '.search-field', function (e) {
            var filter = $(e.currentTarget).val();
            if (filter != '') {
                $('.replicant-site:not([data-value*="' + $(e.currentTarget).val() + '"])').parent().hide();
                $('.replicant-site[data-value*="' + $(e.currentTarget).val() + '"]').parent().show();
            } else {
                $('.replicant-site').parent().show();
            }
        });
    </script>
    <style>
        li ol.breadcrumb {
            margin-bottom: 0px;
            width: 100%;
        }

        label {
            display: block;
        }

        body {
            width: 90%;
            margin: 10px auto;
        }
    </style>
</head>
<body>
<header>
    <h1>Replicant One
        <small>Liste des sites configurés</small>
    </h1>
</header>


<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="form-group">
            <div class="input-group">
            <span class="input-group-addon">
               <i class="fa fa-search"></i>
            </span>
                <input type="text" class="search-field form-control" placeholder="Filtre">
            </div>
        </div>
    </div>
</div>
<hr/>
<div class="row">
    <?php
    $jsonConfiguration = __DIR__ . '/replicant.json';
    $token = sha1($jsonConfiguration);
    $_SESSION['replicant.configurations'][$token] = loadConf($jsonConfiguration);
    ?>
    <div class="col-md-4">
        <a href="index.php?token=<?= $token; ?>"
           class="btn btn-default btn-lg btn-block replicant-site"
           data-value="<?= htmlspecialchars($_SESSION['replicant.configurations'][$token]['name']); ?>">
            <?= $_SESSION['replicant.configurations'][$token]['name']; ?>
        </a>
    </div>
</div>
</body>
</html>
