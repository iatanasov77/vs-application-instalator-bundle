let description = `
  Можете да продължите да ползвате нашия уебсайт без да променяте настройките си, получавайки всички бисквитки, които сайтът използва, 
  или можете да промените своите настройки за бисквитки по всяко време. 
  Ползвайки уебсайта или затваряйки това съобщение, Вие се съгласявате с използването на бисквитки от нас.
`;

export const bg = {
    consentModal: {
        label: 'Съгласие за бисквитки',
        title: 'Ние използваме бисквитки!',
        description:  description,
        revision_message: '<br> Dude, my terms have changed. Sorry for bothering you again!',
        acceptAllBtn: 'Съгласен съм',
        acceptNecessaryBtn: 'Избери кои',
        showPreferencesBtn: 'Управлявайте индивидуалните предпочитания'
    },

    preferencesModal: {
        title: 'Cookie preferences',
        acceptAllBtn: 'Accept all',
        acceptNecessaryBtn: 'Accept necessary only',
        savePreferencesBtn: 'Accept current selection',
        closeIconLabel: 'Close modal',
        sections: []
    }
}
