{% extends "@VSApplication/layout.html.twig" %}

{# Import Macros #}
{% from '@SyliusResource/Macros/actions.html.twig' import create %}

{% if( item.id ) %}
    {% set pageTitle = ( 'vs_application.template.update_title' | trans( {},'VSApplicationBundle' ) ) ~ ' ' ~ ( 'vs_application.template.item_name_single' | trans( {},'VSApplicationBundle' ) ) %}
{% else %}
    {% set pageTitle = ( 'vs_application.template.create_title' | trans( {},'VSApplicationBundle' ) ) ~ ' ' ~ ( 'vs_application.template.item_name_single' | trans( {},'VSApplicationBundle' ) ) %}
{% endif %}

{% block title %}{{ parent() }} :: {{ pageTitle }}{% endblock %}
{% block pageTitle %}<i class="icon_genius"></i> {{ pageTitle }}{% endblock %}

{% block content %}
    <h1>Edit <?= $entity_class_name ?></h1>

    {{ include('<?= $templates_path ?>/_form.html.twig', {'button_label': 'Save'}) }}
{% endblock %}
