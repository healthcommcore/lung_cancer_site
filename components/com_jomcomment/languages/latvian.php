<?php





DEFINE("_JC_","Guest"); 
DEFINE("_JC_GUEST_NAME",        "viesis");

// Templates
DEFINE("_JC_TPL_ADDCOMMENT",    "Komentēt");
DEFINE("_JC_TPL_AUTHOR",        "Lietotāja vārds");
DEFINE("_JC_TPL_EMAIL",         "e-pasts");
DEFINE("_JC_TPL_WEBSITE",       "Web lapa");
DEFINE("_JC_TPL_COMMENT",       "Komentārs");

DEFINE("_JC_TPL_TITLE",       "Virsraksts");
DEFINE("_JC_TPL_WRITTEN_BY",    "autors");

// Warning
DEFINE("_JC_CAPTCHA_MISMATCH",  "Kļūdaina parole");
DEFINE("_JC_INVALID_EMAIL",     "Kļūdaina e-pasta adrese");
DEFINE("_JC_USERNAME_TAKEN",    "Šāds vārds jau ir reģistrēts. Izvēlies citu vārdu.");
DEFINE("_JC_NO_GUEST",          "Piekļuves kļūda. Vispirms reģistrējies");
DEFINE("_JC_IP_BLOCKED",        "Tava IP adrese ir bloķēta");
DEFINE("_JC_DOMAIN_BLOCKED",    "Tavs domēns ir bloķēts");
DEFINE("_JC_MESSAGE_NEED_MOD",  "Komentārs pievienots. To vispirms pārbaudīs administrators.");
DEFINE("_JC_MESSAGE_ADDED",     "Komentārs pievienots!");

// New in 1.3
DEFINE("_JC_TPL_READMORE",       "Lasīt vairāk");
DEFINE("_JC_TPL_COMMENTS",       "Komentāri");   // plural
DEFINE("_JC_TPL_SEC_CODE",       "Rakstīt attēlotās zīmes");   // plural
DEFINE("_JC_TPL_SUBMIT_COMMENTS",       "Pievienot komentāru");   // plural

// New in 1.4
DEFINE("_JC_EMPTY_USERNAME", "Ieraksti savu lietotāja vārdu");
DEFINE("_JC_USERNAME_BLOCKED", "Tavs lietotāja vārds ir bloķēts");
DEFINE("_JC_TPL_WRITE_COMMENT",     "Rakstīt komentāru");
DEFINE("_JC_TPL_GUEST_MUST_LOGIN",  "Lai pievienotu komentāru, jābūt reģistrētam lietotājam. Ja Tev nav lietotāja konta, lūdzu, reģistrējies.");
DEFINE("_JC_TPL_REPORT_POSTING",   "Ziņojums administratoram");

DEFINE("_JC_TPL_NO_COMMENT",   "Šim rakstam vēl nav komentāru...");

// New in 1.5
DEFINE("_JC_TPL_HIDESHOW_FORM",   "Rādīt/slēpt komentāra formu");
DEFINE("_JC_TPL_HIDESHOW_AREA",   "Rādīt/slēpt komentārus");
DEFINE("_JC_TPL_REMEMBER_INFO",   "Atcerēties info?");

// New in 1.6
DEFINE("_JC_TPL_TOO_SHORT",   "Tavs komentārs ir pārāk īss");
DEFINE("_JC_TPL_TOO_LONG",   "Tavs komentārs ir pārāk garš");
DEFINE("_JC_TPL_SUBSCRIBE",   "Saņemt e-pasta ziņu par jaunu komentāru");
DEFINE("_JC_TPL_PAGINATE_NEXT",   "nākošie komentāri");
DEFINE("_JC_TPL_PAGINATE_PREV",   "iepriekšējie komentāri");

// New 1.6.8
DEFINE("_JC_TPL_DUPLICATE",   "Atrasts dubults ieraksts");
DEFINE("_JC_TPL_NOSCRIPT",   "Lai komentētu, jābūt ieslēgtam JavaScript atbalstam");

// New 1.7
DEFINE("_JC_TPL_INPUT_LOCKED", "Komentāri ir slēgti. Tu vairs nevari pievienot komentārus.");
DEFINE("_JC_TPL_TRACKBACK_URI", "Ieraksta sekošanas URI");
DEFINE("_JC_TPL_COMMENT_RSS_URI", "Parakstīties uz šo komentāru RSS barotni");

// New 1.9
// Do not modify {INTERVAL} as it is from configuration
DEFINE("_JC_TPL_REPOST_WARNING", "Vai Tu mēģini spamot? Lūdzu, ievēro '{INTERVAL}' sekunžu intervālu starp komentāriem.");
DEFINE("_JC_TPL_BIGGER", "lielāks");
DEFINE("_JC_TPL_SMALLER", "mazāks");
DEFINE("_JC_VOTE_VOTED", "Tav balss ir pieņemta");
DEFINE("_JC_NOTIFY_ADMIN", "Ziņojums par komentāru ir aizsūtīta administratoram");
DEFINE("_JC_LOW_VOTE","Zemu vērtētie komentāri");
DEFINE("_JC_SHOW_LOW_VOTE","Rādīt");
DEFINE("_JC_VOTE_UP","Balsot pozitīvi");
DEFINE("_JC_VOTE_DOWN","Balsot negatīvi");
DEFINE("_JC_REPORT","Ziņojums");
DEFINE("_JC_TPL_USERSUBSCRIBE","Parakstīties, izmantojot e-pastu (Tikai reģistrētiem lietotājiem)");
DEFINE("_JC_TPL_BOOKMARK","Grāmatzīme");
DEFINE("_JC_TPL_MARKING_FAVORITE","Iezīmē rakstu kā favorītu..");
DEFINE("_JC_TPL_MAILTHIS","Sūtīt pa e-pastu");
DEFINE("_JC_TPL_FAVORITE","Pievienot favorītiem");
DEFINE("_JC_TPL_ADDED_FAVORITE","Šis raksts ir pievienots favorītu sarakstam.");
DEFINE("_JC_TPL_HITS","Skatīts");
DEFINE("_JC_TPL_WARNING_FAVORITE","Šis raksts jau ir Tavu favorītu sarakstā.");
DEFINE("_JC_TPL_LINK_FAVORITE","Skatīt favorītu ierakstus šeit.");
DEFINE("_JC_TPL_DISPLAY_VOTES","Balsis:");
DEFINE("_JC_TPL_MEMBERS_FAV","Atvaino, tikai reģistrētiem lietotājiem.");
DEFINE("_JC_TPL_AGREE_TERMS","Esmu lasījis un piekrītu");
DEFINE("_JC_TPL_LINK_TERMS","Lietošanas noteikumi.");
DEFINE("_JC_TPL_TERMS_WARNING","Lūdzu, apstiprini, ka piekrīti lietošanas noteikumiem.");
DEFINE("_JC_TPL_REPORTS_DUP","Nav atļauts ziņot par komentāru vairāk par vienu reizi");
DEFINE("_JC_TPL_VOTINGS_DUP","Nav atļauts komentāru vērtēt vairāk par vienu reizi");
DEFINE("_JC_TPL_TB_TITLE","Sekošana");
DEFINE("_JC_TPL_DOWN_VOTE","balsot negatīvi");
DEFINE("_JC_TPL_UP_VOTE","balsot pozitīvi");
DEFINE("_JC_TPL_ABUSE_REPORT","ziņot par ļaunprātību");
DEFINE("_JC_TPL_GOLAST_PAGE","Tu vari pievienot komentāru");
DEFINE("_JC_TPL_GOLINK_LAST","šeit");

// New 2.2
DEFINE("_JC_TPL_UNPUBLISH_ADM", "Unpublish");
DEFINE("_JC_TPL_EDIT_ADM", "Edit");

// New
DEFINE("_JC_TPL_RESTRICTED_AREA", "The comment section is restricted to members only.");
DEFINE("_JC_TPL_PREVIEW_COMMENTS", "Preview");
DEFINE("_JC_TPL_ERROR_PREVIEW", "Please enter a comment.");
DEFINE("_JC_TPL_HEAD_PREVIEW", "Comment Preview");

// New 3.0 (Email this popup)
DEFINE("_JC_TITLE_SHARE", "Share this article with a friend");
DEFINE("_JC_FRIEND_EMAIL", "Friends Email");
DEFINE("_JC_YOUR_NAME", "Your Name");
DEFINE("_JC_YOUR_EMAIL", "Your Email");
DEFINE("_JC_EMAIL_SUBJECT", "Message Subject");
DEFINE("_JC_SEND_BUTTON", "Send");
DEFINE("_JC_RESET_BUTTON", "Reset");

// New 3.0 (Bookmark this popup)
DEFINE("_JC_TITLE_BOOKMARKS", "Share this");

// New 3.0 (Favorites popup)
DEFINE("_JC_TITLE_FAVORITES", "Set As Favorite");


DEFINE("_JC_RECAPTCHA_MISMATCH","Invalid recaptcha password");

DEFINE("_JC_TPL_CLOSEWIN","Close");

DEFINE("_JC_TPL_CBMAILTITLE","A user commented on user");

DEFINE("_JC_TPL_SENT_MAIL","An email has been sent to");

DEFINE("_JC_TPL_TERMS_TITLE","Terms &amp; Conditions");
?>