let description = `
  Можете да продължите да ползвате нашия уебсайт без да променяте настройките си, получавайки всички бисквитки, които сайтът използва, 
  или можете да промените своите настройки за бисквитки по всяко време. 
  Ползвайки уебсайта или затваряйки това съобщение, Вие се съгласявате с използването на бисквитки от нас.
`;

export const bg = {
    consent_modal: {
        title: 'Ние използваме бисквитки!',
        description:  description,
        primary_btn: {
            text: 'Съгласен съм',
            role: 'accept_all'              // 'accept_selected' or 'accept_all'
        },
        secondary_btn: {
            text: 'Избери кои',
            role: 'accept_necessary'        // 'settings' or 'accept_necessary'
        }
    },

    settings_modal: {
        title: 'Cookie preferences',
        save_settings_btn: 'Save settings',
        accept_all_btn: 'Accept all',
        reject_all_btn: 'Reject all',
        close_btn_label: 'Close',
        cookie_table_headers: [
            {col1: 'Name'},
            {col2: 'Domain'},
            {col3: 'Expiration'},
            {col4: 'Description'}
        ],
        blocks: [
            {
                title: 'Строго необходими бисквитки',
                description: 'Тези бисквитки са от съществено значение за правилното функциониране на моя уебсайт. Без тези бисквитки уебсайтът няма да работи правилно',
                toggle: {
                    value: 'necessary',
                    enabled: true,
                    readonly: true          // cookie categories with readonly=true are all treated as "necessary cookies"
                }
            },
        ]
    }
}
