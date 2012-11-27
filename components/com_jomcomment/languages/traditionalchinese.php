<?php




DEFINE("_JC_","Guest"); 
DEFINE("_JC_GUEST_NAME",        "訪客");

// Templates
DEFINE("_JC_TPL_ADDCOMMENT",    "發表回應");
DEFINE("_JC_TPL_AUTHOR",        "帳號");
DEFINE("_JC_TPL_EMAIL",         "電子郵件");
DEFINE("_JC_TPL_WEBSITE",       "網站");
DEFINE("_JC_TPL_COMMENT",       "我的意見");

DEFINE("_JC_TPL_TITLE",       "標題");
DEFINE("_JC_TPL_WRITTEN_BY",    "發佈者為");

// Warning
DEFINE("_JC_CAPTCHA_MISMATCH",  "密碼錯誤");
DEFINE("_JC_INVALID_EMAIL",     "不合法的電子郵件地址");
DEFINE("_JC_USERNAME_TAKEN",    "這個帳號已經被其他人註冊了，請使用其他名稱註冊");
DEFINE("_JC_NO_GUEST",          "您沒有使用權限，請先註冊");
DEFINE("_JC_IP_BLOCKED",        "您的IP已經被封鎖了");
DEFINE("_JC_DOMAIN_BLOCKED",    "您的域名已經被封鎖了");
DEFINE("_JC_MESSAGE_NEED_MOD",  "您的回應已發佈，待系統管理員審核後才會顯示");
DEFINE("_JC_MESSAGE_ADDED",     "您的回應已發佈完成");

// New in 1.3
DEFINE("_JC_TPL_READMORE",       "瀏覽全文");
DEFINE("_JC_TPL_COMMENTS",       "回應人次");   // plural
DEFINE("_JC_TPL_SEC_CODE",       "請輸入可以顯示的字元");   // plural
DEFINE("_JC_TPL_SUBMIT_COMMENTS",       "發表回應");   // plural

// New in 1.4
DEFINE("_JC_EMPTY_USERNAME", "請輸入您的帳號");
DEFINE("_JC_USERNAME_BLOCKED", "您的帳號已經被封鎖了");
DEFINE("_JC_TPL_WRITE_COMMENT",     "撰寫回應");
DEFINE("_JC_TPL_GUEST_MUST_LOGIN",  "您必須先登入才可以發表回應，如果您尚未加入會員的話請先完成註冊.");
DEFINE("_JC_TPL_REPORT_POSTING",   "向系統管理員反應");

DEFINE("_JC_TPL_NO_COMMENT",   "目前尚未有任何回應...");

// New in 1.5
DEFINE("_JC_TPL_HIDESHOW_FORM",   "顯示/隱藏 表單");
DEFINE("_JC_TPL_HIDESHOW_AREA",   "顯示/隱藏 回應");
DEFINE("_JC_TPL_REMEMBER_INFO",   "記得我?");

// New in 1.6
DEFINE("_JC_TPL_TOO_SHORT",   "您的回應文字太短");
DEFINE("_JC_TPL_TOO_LONG",   "您的回應文字太長");
DEFINE("_JC_TPL_SUBSCRIBE",   "當有人回應時自動Email通知我");
DEFINE("_JC_TPL_PAGINATE_NEXT",   "下一則回應");
DEFINE("_JC_TPL_PAGINATE_PREV",   "上一則回應");

// New 1.6.8
DEFINE("_JC_TPL_DUPLICATE",   "您輸入了重複的內容");
DEFINE("_JC_TPL_NOSCRIPT",   "您的瀏覽器必須支援JavaScript才能發表回應");

// New 1.7
DEFINE("_JC_TPL_INPUT_LOCKED", "本篇文章已被鎖定，您無法發表回覆.");
DEFINE("_JC_TPL_TRACKBACK_URI", "引用本文的超連結");
DEFINE("_JC_TPL_COMMENT_RSS_URI", "訂閱此回應的RSS");

// New 1.9
// Do not modify {INTERVAL} as it is from configuration
DEFINE("_JC_TPL_REPOST_WARNING", "您想要大量發佈回應嗎? 發布回應請間隔 '{INTERVAL}' 秒");
DEFINE("_JC_TPL_BIGGER", "大一點");
DEFINE("_JC_TPL_SMALLER", "小一點");
DEFINE("_JC_VOTE_VOTED", "完成評價");
DEFINE("_JC_NOTIFY_ADMIN", "本篇回應已通知系統管理員處理中");
DEFINE("_JC_LOW_VOTE","評價低的回應文章");
DEFINE("_JC_SHOW_LOW_VOTE","顯示");
DEFINE("_JC_VOTE_UP","拍手鼓掌");
DEFINE("_JC_VOTE_DOWN","給他噓聲");
DEFINE("_JC_REPORT","統計結果");
DEFINE("_JC_TPL_USERSUBSCRIBE","有新的回應時自動Email通知 (限註冊會員)");
DEFINE("_JC_TPL_BOOKMARK","外部書籤");
DEFINE("_JC_TPL_MARKING_FAVORITE","正在將本文設定到您的外部書籤, 請稍候..");
DEFINE("_JC_TPL_MAILTHIS","轉寄好友");
DEFINE("_JC_TPL_FAVORITE","設為我的最愛");
DEFINE("_JC_TPL_ADDED_FAVORITE","這篇文章已經設定在我的最愛了");
DEFINE("_JC_TPL_HITS","點閱次數");
DEFINE("_JC_TPL_WARNING_FAVORITE","這篇文章已經設定在我的最愛了.");
DEFINE("_JC_TPL_LINK_FAVORITE","檢視我的最愛.");
DEFINE("_JC_TPL_DISPLAY_VOTES","評價:");
DEFINE("_JC_TPL_MEMBERS_FAV","很抱歉，本功能限註冊會員");
DEFINE("_JC_TPL_AGREE_TERMS","我已經閱讀並同意");
DEFINE("_JC_TPL_LINK_TERMS","使用規範.");
DEFINE("_JC_TPL_TERMS_WARNING","請遵守使用規範.");
DEFINE("_JC_TPL_REPORTS_DUP","每篇回應您只能評價一次");
DEFINE("_JC_TPL_VOTINGS_DUP","每篇回應您只能評價一次");
DEFINE("_JC_TPL_TB_TITLE","引用本文");
DEFINE("_JC_TPL_DOWN_VOTE","給他噓聲");
DEFINE("_JC_TPL_UP_VOTE","拍手鼓掌");
DEFINE("_JC_TPL_ABUSE_REPORT","檢舉本篇回應內容");
DEFINE("_JC_TPL_GOLAST_PAGE","您可以發表您的回應");
DEFINE("_JC_TPL_GOLINK_LAST","在此位置");

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