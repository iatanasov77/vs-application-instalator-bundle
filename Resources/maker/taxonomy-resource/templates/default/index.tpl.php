{% extends "@VSApplication/layout.html.twig" %}

{# Import Macros #}
{% import "@VSApplication/Macros/alerts.html.twig" as alerts %}
{% from "@VSApplication/Macros/pagination.html.twig" import pagination %}

{# Override Blocs #}
{% block title %}{{ parent() }} :: List <?= ucfirst( $entity_twig_var_plural ) ?>{% endblock %}
{% block pageTitle %}<i class="icon_genius"></i> List <?= ucfirst( $entity_twig_var_plural ) ?>{% endblock %}

{% block content %}
    <div class="row">
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 mb-5">
            
            {% if items|length > 0 %}
            	{% include '<?= $templates_path ?>/_simpleTreeTable.html.twig' %}
            {% else %}
                {{ alerts.info( 'vs_application.template.there_are_no_items_to_display' | trans( {},'VSApplicationBundle' ) ) }}
            {% endif %}
        
            {% if resources.haveToPaginate() %}
                {{ pagerfanta( resources, null, { 'proximity': 10 } ) }}
                
                {# This Macros Not Needed 
                {{ pagination(resources) }}
                #}
            {% endif %}
        </div>
    </div>
    
    {% include '@VSApplication/Partial/resource-delete.html.twig' %}
{% endblock %}

{% block head_styles %}	
	{{ parent() }}
	
	{#
	{{ encore_entry_link_tags( 'js/projects-categories' ) }}
	#}
{% endblock %}

{% block body_scripts %}
    {{ parent() }}
    
    {#
    {{ encore_entry_script_tags( 'js/projects-categories' ) }}
    #}
{% endblock %}
