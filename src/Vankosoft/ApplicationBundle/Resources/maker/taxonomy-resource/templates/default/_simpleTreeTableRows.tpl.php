<tr data-node-id="{{ item.id }}" {% if parentId %}data-node-pid="{{ item.parent.id }}"{% endif %}>
    <td>{{ item.name }}</td>
    <td>
        {% for locale in translations[item.id] %}
            <i class="flag flag-{{ locale | split( '_' )[1] | lower }}"></i>
            {% if not loop.last %}&nbsp;{% endif %}
        {% endfor %}
    </td>
    <td>
    	<a href="{{ path('vsorg_projects_categories_update', {'id': item.id}) }}">Edit</a>
    </td>
</tr>

{% for child in item.children %}
	{% include 'admin-panel/pages/ProjectsCategories/partial/simpleTreeTableRows.html.twig' with {'parentId': item.id, 'taxon': child} %}   
{% endfor %}
