<?php
session_start();
require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/functions.php');
$app = [];
if(!isset($_SESSION['replicant.configurations'],
    $_GET['token'],
    $_SESSION['replicant.configurations'][$_GET['token']])
) {
    header('Location: ./list.php');
}
if (!isset($app['conf'])) {
    $app['conf'] = $_SESSION['replicant.configurations'][$_GET['token']];
}
$app['source'] = getFileSystem($app['conf']['source']);
$app['destination'] = getFileSystem($app['conf']['destination']);
$app['comparator'] = getComparator($app['source'], $app['destination']);
$app['comparator']->setIgnoreFilePatterns($app['conf']['excludedPatterns']);
$app['createdFiles'] = $app['comparator']->getCreatedFiles();
//var_dump($app['source']->getFiles());
$app['updatedFiles'] = $app['comparator']->getUpdatedFiles();
$app['deletedFiles'] = $app['comparator']->getDeletedFiles();
usort($app['createdFiles'], 'sortByDepthAndAlphanum');
if (isset($_POST['deploy'])) {
    $new = new DateTime();
    $app['conf']['archives']['path'] .= $new->format('Y-m-d_Hi');
    mkdir($app['conf']['archives']['path'], 0755);
    $app['archive'] = getFileSystem($app['conf']['archives']);
    $app['comparator']->setWhiteList(isset($_POST['files']) ? $_POST['files'] : []);
    $app['synchronizer'] = getSynchronizer($app['comparator']);
    $app['synchronizer']->synchronize($app['archive']);
    if (count($app['archive']->getFiles()) == 0) {
        rmdir($app['conf']['archives']['path']);
    }
    $app['comparator']->setWhiteList(null);
    $app['createdFiles'] = $app['comparator']->getCreatedFiles();
    $app['updatedFiles'] = $app['comparator']->getUpdatedFiles();
    $app['deletedFiles'] = $app['comparator']->getDeletedFiles();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Replicant One</title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap-theme.min.css"/>
    <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <script type="text/javascript" src="jquery/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
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
<h1>Replicant One</h1>

<p class="lead text-center"><?= $app['conf']['name']; ?></p>

<div class="row">
    <dl class="col-md-5">
        <dt>Source</dt>
        <dd><?= $app['conf']['source']['path']; ?></dd>
    </dl>
    <div class="col-md-2 text-center">
        <i class="fa fa-angle-right fa-3x" style="color:#eee;"></i>
        <i class="fa fa-angle-right fa-3x" style="color:#888;"></i>
        <i class="fa fa-angle-right fa-3x" style="color:#111;"></i>
    </div>
    <dl class="col-md-5 text-right">
        <dt>Destination</dt>
        <dd><?= $app['conf']['destination']['path']; ?></dd>
    </dl>
    </dl>
</div>
<form method="post">
    <section class=" panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-11">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#create" data-toggle="tab">Ajouté(s)&nbsp;<sup class="badge badge-info"><?= count
                                ($app['createdFiles']); ?></sup></a>
                    </li>
                    <li><a href="#update" data-toggle="tab">Mis à jour&nbsp;<sup class="badge badge-info"><?= count
                                ($app['updatedFiles']);
                                ?></sup></a>
                    </li>
                    <li><a href="#delete" data-toggle="tab">Supprimé(s)&nbsp;<sup class="badge badge-info"><?= count
                                ($app['deletedFiles']);
                                ?></sup></a></li>
                </ul>
                    </div>
                <div class="col-md-1 text-right">
                    <a href="./" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i></a>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="create">
                    <?= renderFileList($app['createdFiles']); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="update">
                    <?= renderFileList($app['updatedFiles']); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="delete">
                    <?= renderFileList($app['deletedFiles']); ?>
                </div>
            </div>
        </div>
    </section>
    <input type="submit" name="deploy" class="btn btn-default" value="Déployez"/>
</form>
</body>
</html>