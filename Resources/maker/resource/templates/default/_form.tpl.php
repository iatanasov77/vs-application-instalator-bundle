{% import "@VSApplication/Macros/form.html.twig" as vs_form %}
{% import "@VSApplication/Macros/alerts.html.twig" as alerts %}

{% if useFormMethod is defined %}{% set formMethod = useFormMethod %}{% else %}{% set formMethod = 'PUT' %}{% endif %}
{{ form_start( form, { 'attr': {'class': 'form-horizontal', 'id': 'form-post'}, 'method': formMethod } ) }}
    {{ form_widget( form._token ) }}
    
    {% if not form.vars.valid %}
        {% for error in form.vars.errors %}
            {{ alerts.error( error.message ) }}
        {%endfor%}
    {% endif %}
    
    {{ form_widget( form ) }}
    
    {# Remove Comment After You Prepare The Form Template
    {{ vs_form.buttons( form, metadata ) }}
    #}
{{ form_end( form, {'render_rest': false} ) }}
