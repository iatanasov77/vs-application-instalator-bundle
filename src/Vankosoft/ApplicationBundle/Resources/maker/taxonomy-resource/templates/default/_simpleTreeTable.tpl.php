<table class="table table-striped table-advance table-hover" id="tblCategories">
	<thead>
        <tr>
            <th>Category</th>
            <th>Locales</th>
            <th>Actions</th>
        </tr>
    </thead>
	<tbody>
        {% for item in items %}
            {% include 'admin-panel/pages/ProjectsCategories/partial/simpleTreeTableRows.html.twig' with {'category': item, 'parentId': 0} %}
            
        {% endfor %}
    </tbody>
</table>
