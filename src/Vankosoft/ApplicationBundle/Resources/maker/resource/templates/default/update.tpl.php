{% extends "@VSApplication/layout.html.twig" %}

{# Import Macros #}
{% from '@SyliusResource/Macros/actions.html.twig' import create %}
{% import "@VSApplication/Macros/form.html.twig" as vs_form %}

{% block title %}{{ parent() }} :: Edit <?= ucfirst( $entity_twig_var_singular ) ?>{% endblock %}
{% block pageTitle %}<i class="icon_genius"></i> Edit <?= ucfirst( $entity_twig_var_singular ) ?>{% endblock %}

{% block content %}
    <h1>Edit <?= $entity_class_name ?></h1>

    {{ include('<?= $templates_path ?>/_form.html.twig', {'button_label': 'Save'}) }}

    <a href="{{ path('<?= $route_name ?>_index') }}">back to list</a>
{% endblock %}
