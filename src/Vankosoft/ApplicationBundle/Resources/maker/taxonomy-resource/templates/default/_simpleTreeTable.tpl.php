<table class="table table-striped table-advance table-hover" id="tblCategories">
	<thead>
        <tr>
        	<th>{{ 'vs_application.template.items_index_row_number' | trans( {},'VSApplicationBundle' ) }}  </th>
        	
<?php foreach ( $entity_fields as $field ): ?>
            <th><?= ucfirst( $field['fieldName'] ) ?></th>
<?php endforeach; ?>

            <th>{{ 'vs_application.template.items_index_row_translations' | trans( {},'VSApplicationBundle' ) }}</th>
            <th>{{ 'vs_application.template.items_index_row_action' | trans( {},'VSApplicationBundle' ) }}</th>
        </tr>
    </thead>
	<tbody>
        {% for item in items %}
            {% include '<?= $templates_path ?>/_simpleTreeTableRows.html.twig' with {'item': item, 'parentId': 0} %}
        {% endfor %}
    </tbody>
</table>
