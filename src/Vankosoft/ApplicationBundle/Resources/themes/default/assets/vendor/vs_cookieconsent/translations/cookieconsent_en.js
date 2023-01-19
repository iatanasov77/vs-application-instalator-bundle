let description = `
  You can continue to use our website without changing your settings, receiving all the cookies that the site uses,
  or you can change your cookie settings at any time.
  By using the website or closing this message, you agree to our use of cookies.
`;

export const en = {
    consent_modal: {
        title: 'We use cookies!',
        description: description,
        revision_message: '<br> Dude, my terms have changed. Sorry for bothering you again!',
        primary_btn: {
            text: 'Accept all',
            role: 'accept_all'              // 'accept_selected' or 'accept_all'
        },
        secondary_btn: {
            text: 'Reject all',
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
                title: 'Strictly necessary cookies',
                description: 'These cookies are essential for the proper functioning of my website. Without these cookies, the website would not work properly',
                toggle: {
                    value: 'necessary',
                    enabled: true,
                    readonly: true          // cookie categories with readonly=true are all treated as "necessary cookies"
                }
            },
        ]
    }
}
