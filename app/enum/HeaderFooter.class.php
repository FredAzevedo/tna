<?php

class HeaderFooter
{
    const HEADERHTMLCSS = '
    <style type="text/css">
        body {
            font: 12pt Georgia, "Times New Roman", Times, serif;
            line-height: 1.3;
        }

        @page {
        /* switch to landscape */
        size: landscape;
        /* set page margins */
        margin: 0.5cm;
        /* Default footers */
        @bottom-left {
            content: "Department of Strategy";
        }
        @bottom-right {
            content: counter(page) " of " counter(pages);
        }
        }

        /* footer, header - position: fixed */
        #header {
        position: fixed;
        width: 100%;
        top: 0;
        left: 0;
        right: 0;
        }

        #footer {
            position: fixed;
            width: 100%;
            bottom: 0;
            left: 0;
            right: 0;
        }

        /* Fix overflow of headers and content */
        body {
            padding-top: 50px;
        }
        .custom-page-start {
            margin-top: 50px;
        }

        .custom-footer-page-number:after {
            content: counter(page);
        }
    </style>
    ';

    const HEADEROPEN = '<div id="header">';
    const HEADERCLOSE = '</div>';

    const FOOTEROPEN = '<div id="footer">';
    const FOOTERCLOSE = '<span class="custom-footer-page-number">Number: </span></ div>';
}