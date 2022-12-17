{% extends "@VSApplication/layout.html.twig" %}

{% block content %}
    <h1><?= $entity_class_name ?></h1>

    <table class="table">
        <tbody>
<?php foreach ($entity_fields as $field): ?>
            <tr>
                <th><?= ucfirst($field['fieldName']) ?></th>
                <td>{{ <?= $helper->getEntityFieldPrintCode($entity_twig_var_singular, $field) ?> }}</td>
            </tr>
<?php endforeach; ?>
        </tbody>
    </table>

    <a href="{{ path('<?= $route_name ?>_index') }}">back to list</a>

    <a href="{{ path('<?= $route_name ?>_update', {'<?= $entity_identifier ?>': <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>}) }}">edit</a>
{% endblock %}
