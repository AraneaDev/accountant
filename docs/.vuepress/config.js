module.exports = {
    title: 'Accountant',
    description: 'Accountability package for the Eloquent ORM.',
    base: '/accountant/',
    dest: 'public',

    head: [
        ['meta', { name: 'keywords', content: 'accountability, accountable, accountant, audit, auditing, changes, eloquent, history, log, logging, laravel, ledger, liable, lumen, record' }]
    ],

    plugins: [
        '@vuepress/back-to-top',
        '@vuepress/active-header-links'
    ],

    themeConfig: {
        repo: 'https://gitlab.com/altek/accountant',
        repoLabel: 'Contribute!',
        editLinks: true,
        editLinkText: 'Improve this page!',
        docsDir: 'docs',

        nav: [
            { text: 'Home', link: '/' },
            { text: 'Packagist', link: 'https://packagist.org/packages/altek/accountant' },
            { text: 'Laravel', link: 'https://laravel.com' }
        ],

        sidebar: [
            '/',

            {
                title: 'Getting Started',
                collapsable: false,
                children: [
                    ['installation', 'Installation'],
                    ['configuration', 'Configuration'],
                    ['upgrading', 'Upgrading']
                ]
            },

            {
                title: 'Recordable',
                children: [
                    ['recordable-model-setup', 'Model Setup'],
                    ['recordable-configuration', 'Configuration']
                ]
            },

            {
                title: 'Ledger',
                children: [
                    ['ledger-retrieval', 'Retrieval'],
                    ['ledger-table', 'Table'],
                    ['ledger-implementation', 'Implementation'],
                    ['ledger-drivers', 'Drivers'],
                    ['ledger-events', 'Events']
                ]
            },

            {
                title: 'Advanced',
                children: [
                    ['data-integrity-check', 'Data Integrity Check'],
                    ['ledger-extra', 'Ledger Extra'],
                    ['recordable-extraction', 'Recordable Extraction'],
                    ['resolvers', 'Resolvers'],
                    ['ciphers', 'Ciphers'],
                    ['accountant', 'Accountant']
                ]
            },

            {
                title: 'Help',
                collapsable: false,
                children: [
                    ['troubleshooting', 'Troubleshooting']
                ]
            }
        ]
    }
};
