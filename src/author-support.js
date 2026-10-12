import ClipboardJS from 'clipboard';
import TypeIt from 'typeit';
import { initializeAuthorSupport } from '../assets/js/author-support.js';
import genericCardIcon from '../assets/images/payment-card-generic.svg';
import mastercardIcon from '../assets/images/payment-card-mastercard.svg';
import unionCardIcon from '../assets/images/payment-card-union.svg';
import visaCardIcon from '../assets/images/payment-card-visa.svg';
import monobankIcon from '../assets/images/payment-monobank.svg';
import privatbankIcon from '../assets/images/payment-privatbank.svg';

initializeAuthorSupport(ClipboardJS, TypeIt, {
    bankIcons: {
        monobank: monobankIcon,
        privatbank: privatbankIcon,
    },
    cardIcons: {
        master: mastercardIcon,
        mastercard: mastercardIcon,
        other: genericCardIcon,
        union: unionCardIcon,
        visa: visaCardIcon,
    },
});
