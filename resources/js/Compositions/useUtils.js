import {date} from "quasar";

export default function () {
    const formatMoney = value => {
        return (new Intl.NumberFormat('en-IN', {style: 'currency', currency: 'INR'}).format(Number(value)));
    }

    const formatDate = (value, pattern) => {
        try {
            if (!value) return 'N/A'   // ✅ handle null/undefined/empty
            if (pattern) {
                return date.formatDate(new Date(value), pattern)
            }
            return date.formatDate(new Date(value), 'DD MMM YYYY')
        } catch (er) {
            return 'Invalid Date'
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

    const employment_types = [
        'MR',
        'PE',
        'Deleted',
    ];

    const educationalQualifications = [
        'U/M',
        'HSLC',
        'HSSLC',
        'Graduate & Level',
        'Master Degree & Level',
    ];

    const skills = [
        'Skilled-II',
        'Skilled-I',
        'Semi-Skilled',
        'Unskilled',
    ];


    const emailRegex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    const mobileRegex = /^[0-9]{10}$/;

    return {
        employment_types,
        educationalQualifications,
        skills,
        formatMoney,
        formatAudioTime,
        formatDate,
        formatDateTime,
        emailRegex,
        mobileRegex,

    }
};
