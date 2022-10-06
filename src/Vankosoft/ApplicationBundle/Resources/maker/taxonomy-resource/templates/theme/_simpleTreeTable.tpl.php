<table class="table table-striped table-advance table-hover" id="tblCategories">
	<thead>
        <tr>
<?php foreach ( $entity_fields as $field ): ?>
            <th><?= ucfirst( $field['fieldName'] ) ?></th>
<?php endforeach; ?>

            <th>Locales</th>
            <th>Actions</th>
        </tr>
    </thead>
	<tbody>
        {% for item in items %}
            {% include '<?= $templates_path ?>/_simpleTreeTableRows.html.twig' with {'item': item, 'parentId': 0} %}
        {% endfor %}
    </tbody>
</table>
