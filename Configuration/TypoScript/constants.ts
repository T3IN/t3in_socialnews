#plugin.tx_news.rss.channel.link = http://domian.tld

module.tx_t3insocialnews_socialpost {
    view {
        # cat=module.tx_t3insocialnews_socialpost/file; type=string; label=Path to template root (BE)
        templateRootPath = EXT:t3in_socialnews/Resources/Private/Backend/Templates/
        # cat=module.tx_t3insocialnews_socialpost/file; type=string; label=Path to template partials (BE)
        partialRootPath = EXT:t3in_socialnews/Resources/Private/Backend/Partials/
        # cat=module.tx_t3insocialnews_socialpost/file; type=string; label=Path to template layouts (BE)
        layoutRootPath = EXT:t3in_socialnews/Resources/Private/Backend/Layouts/
    }
    persistence {
        # cat=module.tx_t3insocialnews_socialpost//a; type=string; label=Default storage PID
        storagePid =
    }
}
