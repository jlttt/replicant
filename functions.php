<?php
function loadConf($confPath)
{
    return json_decode(file_get_contents($confPath), true);
}

function getFileSystem($fsConf)
{
    switch ($fsConf['type']) {
        case 'hybrid':
            $fileSystem = new \Jlttt\Deploy\FileSystem\FlySystemAdapter(
                new \League\Flysystem\Filesystem(
                    new \League\Flysystem\Sftp\SftpAdapter([
                        'host' => $fsConf['host'],
                        'port' => $fsConf['port'],
                        'username' => $fsConf['username'],
                        'password' => $fsConf['password'],
                        'root' =>  $fsConf['path'],
                        'timeout' => 10,
                    ])
                )
            );
            $fileExplorer = new \Jlttt\Deploy\FileSystem\WebFileExplorer($fileSystem, $fsConf['webPath'], $fsConf['baseUrl']);
            return new \Jlttt\Deploy\FileSystem\HybridFileSystem($fileSystem, $fileExplorer);
            break;
        case 'sftp' :
            return new \Jlttt\Deploy\FileSystem\FlySystemAdapter(
                new \League\Flysystem\Filesystem(
                    new \League\Flysystem\Sftp\SftpAdapter([
                        'host' => $fsConf['host'],
                        'port' => $fsConf['port'],
                        'username' => $fsConf['username'],
                        'password' => $fsConf['password'],
                        'root' =>  $fsConf['path'],
                        'timeout' => 10,
                    ])
                )
            );
            break;
        case 'local':
        default:
            return new \Jlttt\Deploy\FileSystem\FlySystemAdapter(
                new \League\Flysystem\Filesystem(
                    new \League\Flysystem\Adapter\Local($fsConf['path'])
                )
            );
    }
}

function getComparator($source, $destination)
{
   return new \Jlttt\Deploy\FileSystem\Comparator($source, $destination);
}

function renderFileList($fileList)
{
    if (!empty($fileList)) {
        include(__DIR__ . '/templates/fileList.php');
    } else {
        include(__DIR__ . '/templates/emptyFileList.php');
    }

}

function renderFile(\Jlttt\Deploy\FileSystem\FileInterface $file)
{
    $path =  $file->getPath();
    $parts = explode('/', $path);
    include(__DIR__ . '/templates/file.php');
}

function getSynchronizer($comparator)
{
    return new \Jlttt\Deploy\FileSystem\Synchronizer($comparator);
}

function sortByDepthAndAlphanum(\Jlttt\Deploy\FileSystem\FileInterface $a, \Jlttt\Deploy\FileSystem\FileInterface $b) {
    $depthGap = substr_count($a->getPath(), '/') - substr_count($b->getPath(), '/');
    if ($depthGap == 0) {
        return strcmp($a->getPath(), $b->getPath());
    }
    return $depthGap;
}

function glob_recursive($pattern, $flags = 0)
{
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
    {
        $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}