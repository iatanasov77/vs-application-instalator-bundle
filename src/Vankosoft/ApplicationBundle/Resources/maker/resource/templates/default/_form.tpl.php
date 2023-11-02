{% import "@VSApplication/Macros/form.html.twig" as vs_form %}

{% if useFormMethod is defined %}{% set formMethod = useFormMethod %}{% else %}{% set formMethod = 'PUT' %}{% endif %}
{{ form_start( form, { 'attr': {'class': 'form-horizontal', 'id': 'form-post'}, 'method': formMethod } ) }}
    {{ form_widget( form._token ) }}
    
    {% if form_errors(form) is not empty %}
        <div class="alert alert-block alert-danger fade in">
            <button type="button" class="close close-sm" data-bs-dismiss="alert">
                <i class="icon-remove"></i>
            </button>
            <strong>{{ 'vs_application.form.errors_title' | trans( {},'VSApplicationBundle' ) }}</strong> {{ form_errors( form ) }}
        </div>
    {% endif %}
    
    {{ form_widget( form ) }}
    
    {# Remove Comment After You Prepare The Form Template
    {{ vs_form.buttons( form, metadata ) }}
    #}
{{ form_end( form, {'render_rest': false} ) }}
