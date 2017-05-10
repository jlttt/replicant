<li><label>
        <ol class="breadcrumb">
            <li><input type="checkbox" name="files[]" value="<?= $path; ?>" checked></li>
            <?php foreach ($parts as $part) { ?>
                <li><?= $part; ?></li>
            <?php } ?>
        </ol>
    </label>
</li>