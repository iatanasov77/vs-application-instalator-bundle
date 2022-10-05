{% extends "@VSApplication/layout.html.twig" %}

{# Import Macros #}
{% import "@VSApplication/Macros/alerts.html.twig" as alerts %}
{% from "@VSApplication/Macros/pagination.html.twig" import pagination %}

{# Override Blocs #}
{% block title %}{{ parent() }} :: List <?= ucfirst( $entity_twig_var_plural ) ?>{% endblock %}
{% block pageTitle %}<i class="icon_genius"></i> List <?= ucfirst( $entity_twig_var_plural ) ?>{% endblock %}

{% block content %}
    <h1><?= $entity_class_name ?> index</h1>

	<div class="row">
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 mb-5">
            <table class="table">
                <thead>
                    <tr>
<?php foreach ($entity_fields as $field): ?>
                		<th><?= ucfirst($field['fieldName']) ?></th>
<?php endforeach; ?>
                        <th>actions</th>
                    </tr>
                </thead>
                <tbody>
        {% for item in resources %}
                    <tr>
<?php foreach ($entity_fields as $field): ?>
                        <td>{{ <?= $helper->getEntityFieldPrintCode( 'item', $field ) ?> }}</td>
<?php endforeach; ?>
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-primary" href="{{ path('<?= $route_name ?>_show', {'id': item.id}) }}">
                                    <i class="fas fa-eye" style="color: #be4bdb;" ></i>
                                </a>
                                <a class="btn btn-primary" href="{{ path('<?= $route_name ?>_update', {'id': item.id}) }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a class="btn btn-danger btnDelete" 
                                    href="{{ path('<?= $route_name ?>_delete', {'id': item.id}) }}"
                                    data-csrfToken="{{ csrf_token( item.id ) }}"
                                    title="Delete"
                                >
                                    <i class="icon_close_alt2"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
        {% else %}
                    <tr>
                        <td colspan="<?= (count($entity_fields) + 1) ?>">{{ alerts.info('sylius.ui.there_are_no_mappers_to_display'|trans) }}</td>
                    </tr>
        {% endfor %}
                </tbody>
            </table>
            
            {% if resources.haveToPaginate() %}
                {{ pagerfanta( resources, null, { 'proximity': 10 } ) }}
            {% endif %}
            
        </div>
    </div>

	{% include '<?= $templates_path ?>/_delete_form.html.twig' %}
{% endblock %}

{% block head_styles %}
    {{ parent() }}
    
    {# {{ encore_entry_link_tags( 'js/projects' ) }} #}
{% endblock %}

{% block body_scripts %}
    {{ parent() }}
    
    {# {{ encore_entry_script_tags( 'js/projects' ) }} #}
{% endblock %}
