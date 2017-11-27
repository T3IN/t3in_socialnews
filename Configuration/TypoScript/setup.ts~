
#########################################################
## copy to root template; set detailPid, startingpoint ##
#########################################################
[globalVar = TSFE:type = 9818]

config {
    disableAllHeaderCode = 1
    xhtml_cleaning = none
    admPanel = 0
    debug = 0
    disablePrefixComment = 1
    metaCharset = utf-8
    # before CMS 8 (adjust if using ATOM)
    additionalHeaders = Content-Type:application/rss+xml;charset=utf-8
    # CMS 8 (adjust if using ATOM)
    additionalHeaders.10.header = Content-Type:application/rss+xml;charset=utf-8
    absRefPrefix = {$plugin.tx_news.rss.channel.link}
}

pageNewsRSS = PAGE
pageNewsRSS {
    typeNum = 9818
    10 < tt_content.list.20.news_pi1
    10 {
            switchableControllerActions {
                    News {
                            1 = list
                    }
            }
            settings < plugin.tx_news.settings
            settings {
                    limit = 0
                    detailPid = 12
                    startingpoint = 15
                    format = xml
            }
    }
}
[global]
## end-copy ##


# custom xml template for news 
plugin.tx_news.view.templateRootPaths {
 20 = EXT:t3in_socialnews/Resources/Private/Templates/News/
}


# Module configuration
module.tx_t3insocialnews_tools_t3insocialnewssocialpost {
    persistence {
        storagePid = {$module.tx_t3insocialnews_socialpost.persistence.storagePid}
    }
    settings {
    	app_id = 
    	app_secret = 
    	newsXmlUrl = 
    }
    view {
        templateRootPaths.0 = EXT:t3in_socialnews/Resources/Private/Backend/Templates/
        templateRootPaths.1 = {$module.tx_t3insocialnews_socialpost.view.templateRootPath}
        partialRootPaths.0 = EXT:t3in_socialnews/Resources/Private/Backend/Partials/
        partialRootPaths.1 = {$module.tx_t3insocialnews_socialpost.view.partialRootPath}
        layoutRootPaths.0 = EXT:t3in_socialnews/Resources/Private/Backend/Layouts/
        layoutRootPaths.1 = {$module.tx_t3insocialnews_socialpost.view.layoutRootPath}
    }
}
