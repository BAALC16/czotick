var dropify_config = {
  messages: {
    'default': 'Glissez-déposez ou cliquer pour ajouter',
    'replace': 'Glissez-déposez ou cliquer pour remplacer',
    'remove':  'Retirer',
    'error':   'Ouups, il y a un problème. Veuillez choisir un autre fichier.'
  }, error: {
    'fileSize': 'Le fichier est trop gros ({{ value }} max).',
    'minWidth': 'L\'image n\'est pas assez large ({{ value }}}px min).',
    'maxWidth': 'L\'image est trop large ({{ value }}}px max).',
    'minHeight': 'L\'image n\'est pas assez grande ({{ value }}}px min).',
    'maxHeight': 'L\'image est trop grande ({{ value }}px max).',
    'imageFormat': 'Mauvais format d\'image (seulement {{ value }} acceptés).',
    'fileExtension': 'Mauvais type de fichier (seulement {{ value }} acceptés).'
  }, tpl: {
    wrap:            '<div class="dropify-wrapper"></div>',
    loader:          '<div class="dropify-loader"></div>',
    message:         '<div class="dropify-message"><span class="file-icon"></span> <p>{{ default }}</p></div>',
    preview:         '<div class="dropify-preview"><span class="dropify-render"></span><div class="dropify-infos"><div class="dropify-infos-inner"><p class="dropify-infos-message">{{ replace }}</p></div></div></div>',
    filename:        '<p class="dropify-filename"><span class="file-icon"></span> <span class="dropify-filename-inner"></span></p>',
    clearButton:     '<button type="button" class="dropify-clear">{{ remove }}</button>',
    errorLine:       '<p class="dropify-error">{{ error }}</p>',
    errorsContainer: '<div class="dropify-errors-container"><ul></ul></div>'
  }
}


if(typeof(moment) != 'undefined') {
  moment.updateLocale('fr', {
    months : 'Janvier_Février_Mars_Avril_Mai_Juin_Juillet_Août_Septembre_Octobre_Novembre_Décembre'.split('_'),
    monthsShort : 'Janv._Févr._Mars_Avr._Mai_Juin_Juil._Août_Sept._Oct._Nov._Déc.'.split('_'),
    monthsParseExact : true,
    weekdays : 'Dimanche_Lundi_Mardi_Mercredi_Jeudi_Vendredi_Samedi'.split('_'),
    weekdaysShort : 'Dim._Lun._Mar._Mer._Jeu._Ven._Sam.'.split('_'),
    weekdaysMin : 'Di_Lu_Ma_Me_Je_Ve_Sa'.split('_'),
    weekdaysParseExact : true,
    longDateFormat : {
      LT : 'HH:mm',
      LTS : 'HH:mm:ss',
      L : 'DD/MM/YYYY',
      LL : 'D MMMM YYYY',
      LLL : 'D MMMM YYYY HH:mm',
      LLLL : 'dddd D MMMM YYYY HH:mm'
    },
    calendar : {
      sameDay : '[Aujourd’hui à] LT',
      nextDay : '[Demain à] LT',
      nextWeek : 'dddd [à] LT',
      lastDay : '[Hier à] LT',
      lastWeek : 'dddd [dernier à] LT',
      sameElse : 'L'
    },
    relativeTime : {
      future : 'dans %s',
      past : 'il y a %s',
      s : 'quelques secondes',
      m : 'une minute',
      mm : '%d minutes',
      h : 'une heure',
      hh : '%d heures',
      d : 'un jour',
      dd : '%d jours',
      M : 'un mois',
      MM : '%d mois',
      y : 'un an',
      yy : '%d ans'
    },
    dayOfMonthOrdinalParse : /\d{1,2}(er|e)/,
    ordinal : function (number) {
      return number + (number === 1 ? 'er' : 'e');
    },
    meridiemParse : /PD|MD/,
    isPM : function (input) {
      return input.charAt(0) === 'M';
    },
    // In case the meridiem units are not separated around 12, then implement
    // this function (look at locale/id.js for an example).
    // meridiemHour : function (hour, meridiem) {
      //     return /* 0-23 hour, given meridiem token and hour 1-12 */ ;
      // },
      meridiem : function (hours, minutes, isLower) {
        return hours < 12 ? 'PD' : 'MD';
      },
      week : {
        dow : 1, // Monday is the first day of the week.
        doy : 4  // Used to determine first week of the year.
      }
    });

    moment.locale("fr");
}

waitMe_config = {
  effect : 'bouncePulse',
	text : 'Chargement en cours...',
  bg : 'rgba(0,0,0,0.5)',
  color : '#0ec6d5',
  maxSize : '',
  waitTime : -1,
  textPos : 'vertical',
  fontSize : '',
  source : '',
  // onClose : function() {}
}
