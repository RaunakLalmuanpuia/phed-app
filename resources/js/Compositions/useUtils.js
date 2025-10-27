import {date} from "quasar";

export default function () {
    const formatMoney = value => {
        return (new Intl.NumberFormat('en-IN', {style: 'currency', currency: 'INR'}).format(Number(value)));
    }

   // Output-  09 Sep 2025
    // const formatDate = (value, pattern) => {
    //     try {
    //         if (!value) return 'N/A'   // ✅ handle null/undefined/empty
    //         if (pattern) {
    //             return date.formatDate(new Date(value), pattern)
    //         }
    //         return date.formatDate(new Date(value), 'DD MMM YYYY')
    //     } catch (er) {
    //         return 'Invalid Date'
    //     }
    // }

    // Output - 09-09-2025
    const formatDate = (value, pattern) => {
        try {
            if (!value) return 'N/A'   // ✅ handle null/undefined/empty
            if (pattern) {
                return date.formatDate(new Date(value), pattern)
            }
            // Default to dd-mm-yyyy
            return date.formatDate(new Date(value), 'DD-MM-YYYY')
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
        'WC',
        'Deleted',
    ];

    const designations = [
        'Asst.Mechanic',
        'Carpenter',
        'DEO',
        'Driver',
        'Electric Lineman',
        'Electrician-II',
        'Group-D',
        'LDC',
        'Plant Operator',
        'Plumber',
        'Pump Operator',
        'SA',
        'Sample Collector',
        'Surveyor',
        'Switchboard Operator',
        'Welder',
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

    const pay_matrix = [
        'Level 1 (17,400 - 38,600)',
        'Level 2 (19,900 - 44,400)',
        'Level 3 (21,700 - 48,500)',
        'Level 4 (25,500 - 56,800)',
        'Level 5 (29,200 - 64,700)',
    ];


    const emailRegex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    const mobileRegex = /^[0-9]{10}$/;

    return {
        employment_types,
        designations,
        educationalQualifications,
        skills,
        pay_matrix,
        formatMoney,
        formatAudioTime,
        formatDate,
        formatDateTime,
        emailRegex,
        mobileRegex,

    }
};
