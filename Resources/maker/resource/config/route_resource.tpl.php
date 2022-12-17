<?= $route_id ?>:<?= PHP_EOL ?>
    resource: |<?= PHP_EOL ?>
        alias: <?= $route_alias ?><?= PHP_EOL ?>
        #except: ['show']<?= PHP_EOL ?>
        path: <?= $route_path ?><?= PHP_EOL ?>
        templates: "<?= $route_templates_path ?>"<?= PHP_EOL ?>
    type: sylius.resource<?= PHP_EOL ?>