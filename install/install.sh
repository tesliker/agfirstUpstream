#!/bin/bash

# This is only meant to be run once, from within the newly cloned project directory
#
# bash scripts/install.sh

# make sure we start in the project's root directory
ROOT_DIR=$(dirname $(dirname $(readlink -f $0)))

pushd $ROOT_DIR > /dev/null

    cat install/banner.txt
    echo
    echo -n " Domain name (Ex. www.example.com ): "
    read DOMAIN
    
    #create random admin password
    ADMIN_PASS=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 16 | head -n 1)
    
    pushd web > /dev/null

        #add sitemap to robots.txt
        sed "s/%DOMAIN%/${DOMAIN}/g"   < ../install/robotstxt.settings.yml.TEMPLATE > ../install/config/robotstxt.settings.yml

        # gotta be able to write to these dirs and their children
        chmod -R u+w sites/default
        chmod -R u+w libraries

        # enable our standard modules
        drush pm:enable \
            address, \
            admin_toolbar, \
            admin_toolbar_tools, \
            agfirst_content_log, \
            agfirst_embedded_forms, \
            agfirst_extlink_override, \
            anchor_link, \
            better_exposed_filters, \
            content_moderation, \
            crop, \
            ctools, \
            cyberwoven_admin, \
            cyberwoven_theme_suggestions, \
            cyberwoven_ux, \
            devel, \
            editor_advanced_link, \
            entity_reference_revisions, \
            environment_indicator, \
            extlink, \
            field_group, \
            honeypot, \
            image_url_formatter, \
            image_widget_crop, \
            imce, \
            inline_entity_form, \
            linkit, \
            masquerade, \
            menu_block, \
            metatag, \
            metatag_open_graph, \
            metatag_verification, \
            paragraphs, \
            pathauto, \
            redirect, \
            reroute_email, \
            robotstxt, \
            smart_trim, \
            video_embed_wysiwyg, \
            webform, \
            webform_ui, \
            workflows, \
            xmlsitemap \
            --yes

        # disable modules
        drush pm:uninstall \
            quickedit \
            contact \
            --yes

        drush core:cron
        drush cache:rebuild
        drush config:import --partial --source=../install/config/ --yes
        drush config:export --yes
    popd

popd