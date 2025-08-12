<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Engagement Card</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            margin: 0;
            background: white;
        }
        .page {
            min-height: 257mm;
            box-sizing: border-box;
            padding: 0;
        }
        p { margin: 0; line-height: 1.4; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .spacer-sm { margin-top: 8px; }
        .spacer-md { margin-top: 15px; }
        .right-align { text-align: right; }

        .signature-block {
            width: 200px;
            text-align: center;
            font-weight: normal;
            float: right;
            margin-top: 50px;
            margin-right: 20mm;
            clear: both;
        }
        .signature-block img {
            max-width: 150px;
            height: auto;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
<div class="page">

    <p class="right-align bold">ANNEXURE 1</p>
    <div class="spacer-sm"></div>

    <p class="center bold">No.A.21022/50/2020-E-in-C/PHE/</p>
    <div class="spacer-sm"></div>

    <p class="center bold">
        GOVERNMENT OF MIZORAM<br>
        OFFICE OF THE ENGINEER-IN-CHIEF : PHED<br>
        MIZORAM: AIZAWL
    </p>
    <div class="spacer-md"></div>

    <p class="center bold" style="text-decoration: underline;">
        ENGAGEMENT CARD FOR PROVISIONAL EMPLOYEE
    </p>
    <div class="spacer-md"></div>

    <p class="right-align bold">
        Engagement Card No: <span style="text-decoration: underline;">{{ $card_no }}</span>
    </p>
    <p class="right-align bold">
        Date: <span style="text-decoration: underline;">{{ $date }}</span>
    </p>
    <div class="spacer-md"></div>

    <p>
        <strong>
            <span style="text-decoration: underline;">{{ $name }}</span>, (Date of Birth)
            <span style="text-decoration: underline;">{{ $dob }}</span> s/o
            <span style="text-decoration: underline;">{{ $parent_name }}</span>, House No.
            <span style="text-decoration: underline;">{{ $address }}</span>
        </strong>
        is hereby engaged on temporary/provisional basis as detailed below:
    </p>
    <div class="spacer-md"></div>

    <p class="bold">1.&emsp;Name of Post&nbsp;:&nbsp;{{ $post }}</p>
    <p>2.&emsp;Corresponding Pay in the Pay Matrix&nbsp;:&nbsp;{{ $pay_matrix }}</p>
    <p>3.&emsp;Monthly Lumpsum Remuneration&nbsp;:&nbsp;Rs {{ $remuneration }}/-</p>
    <p class="bold">4.&emsp;Period of Engagement&nbsp;:&nbsp;{{ $start_date }} to {{ $end_date }}</p>


    <p>5.&emsp;Government’s Approval No. &amp; Date</p>
    <p style="white-space: pre-wrap; font-weight: bold;">    (a)   DP&amp;AR : {{ $approval_dpar }}</p>
    <p style="white-space: pre-wrap; font-weight: bold;">    (b)   Finance Department : {{ $approval_fin }}</p>

    <div class="spacer-sm"></div>

    <p>6.&emsp;Debitable head of account&nbsp;:&nbsp;{{ $account_head_1 }}</p>
    <div style="padding-left: 12em; margin-top: 2px;">
        <p>{{ $account_head_2 }}</p>
        <p>{{ $account_head_3 }}</p>
        <p>{{ $account_head_4 }}</p>
        <p>{{ $account_head_5 }}</p>
        <p>{{ $account_head_6 }}</p>
    </div>

    <div class="signature-block">
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/e_in_c_signature.png'))) }}" alt="Signature" />
        <p class="bold">Engineer-in-Chief: PHED</p>
        <p class="bold">Mizoram: Aizawl</p>
    </div>

</div>
</body>
</html>
