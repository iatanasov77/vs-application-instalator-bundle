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
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <h5 class="card-header">Game Category</h5>
                <div class="card-body">
    				{{ include('<?= $templates_path ?>/_form.html.twig', {'button_label': 'Save'}) }}
                </div>
            </div>
        </div>
    </div>
{% endblock %}
