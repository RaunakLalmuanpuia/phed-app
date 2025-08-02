import {date} from "quasar";

export default function () {
    const formatMoney = value => {
        return (new Intl.NumberFormat('en-IN', {style: 'currency', currency: 'INR'}).format(Number(value)));
    }

    // const paytmUrl = () => {
    //     return (true) ? 'https://securegw-stage.paytm.in/merchantpgpui/checkoutjs/merchants/Mizora32691755815703.js'
    //         : 'https://securegw.paytm.in/merchantpgpui/checkoutjs/merchants/Mizora49434774766128.js';
    // };

    const paytmUrl = () => {
        return (true) ? 'https://securestage.paytmpayments.com/merchantpgpui/checkoutjs/merchants/Resell00448805757124.js'
            : 'https://secure.paytmpayments.com/merchantpgpui/checkoutjs/merchants/Mizora41210038573029.js';
    };

    const formatDate = (value, pattern) => {
        try {
            if (pattern) {
                return date.formatDate(new Date(value), pattern);
            }
            return date.formatDate(new Date(value), 'DD MMM YYYY');
        } catch (er) {
            return 'invalid Date'
        }
    }
    const formatAudioTime = secs => {
        let minutes = Math.floor(secs / 60) || 0;
        let seconds = Math.floor(secs - minutes * 60) || 0;
        return (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
    }


    const formatDateTime = value => {
        try {
            return date.formatDate(new Date(value), 'DD MMM YYYY hh:mm a')
        } catch (er) {
            return 'invalid Date'
        }
    }
    const DEFAULT_TEST_PARAMS = {
        chest: '35 cm',
        expanded_chest: '38 cm',
        height: '165 cm',
        long_jump: '3 Meter',
        high_jump: '3 Meter',
        short_race: '10 Sec',
        long_race: '20 Min',
    }
    const emailRegex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    const mobileRegex = /^[0-9]{10}$/;
    return {
        DEFAULT_TEST_PARAMS,
        formatMoney,
        formatAudioTime,
        formatDate,
        formatDateTime,
        emailRegex,
        mobileRegex,
        paytmUrl
    }
};
