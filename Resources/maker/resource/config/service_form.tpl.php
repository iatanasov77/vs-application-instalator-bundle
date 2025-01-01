vsapp.form.<?= $resource_name ?>:
    public: true
    class: App\Form\ProjectForm
    arguments:
        - '%vsapp.model.<?= $resource_name ?>.class%'
        - '@request_stack'
    tags: ['form.type']
