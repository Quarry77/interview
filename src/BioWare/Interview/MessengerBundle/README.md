MessengerBundle
==============

This bundle provides a small restful service that supports a very simple messaging system between friends.

Requirements: 
    Symfony 2
    PHP5
    Composer
    HWI OAUTH Bundle
    SQLite



Installation
------------

In addition to this it's own files which should be placed in the "src" or "vendor" folders of symfony, this bundle requires the HWI OAuth Bundle in order to authenticate with Facebook. To attain the HWI bundle, add the following to your composer.json file:

// composer.json
"require": {
    ...,
    "hwi/oauth-bundle": "0.4.*@dev"
}

Then run "composer install" or "composer update" as neccessary to have the bundle retrieved. 
Next, update AppKernel.php know about the new bundles:

// app/AppKernel.php
$bundles = array(
    ...,
    new BioWare\Interview\MessengerBundle\BioWareInterviewMessengerBundle(),
    new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
);


Then update config.yml to set the database settings, hwi firewall and user provider service for the messenger. For the doctrine settings, the only changes are the driver, name and the addition of a path line for sqlite. It may be helpful if the name and path are the same. The relevent sections are shown here:

// app/config/config.yml
doctrine:
    dbal:
        driver:   "pdo_sqlite"
        ...
        dbname:   "%kernel.root_dir%/messenger.sqlite"
        ...
        path:     "%kernel.root_dir%/messenger.sqlite"

hwi_oauth:
    # name of the firewall in which this bundle is active, this setting MUST be set
    firewall_name: secured_area
    resource_owners:
        facebook:
            type:                facebook
            client_id:           629237347192080
            client_secret:       a015512dc980e6a19fc71f4b6ef35b68

services:
    messenger_user_provider:
        class: BioWare\Interview\MessengerBundle\Provider\UserProvider
        arguments: ["@doctrine.orm.entity_manager"]


Next, update security.yml to edit the providers, firewalls and access controll settings:

// app/config/security.yml
security:
    ...
    providers:
        ...
        user_provider:
            id: messenger_user_provider

    firewalls:
        ...
        secured_area:
            pattern: ^/
            anonymous: true
            oauth:
                resource_owners:
                    facebook: "/login/check-facebook"
                login_path:        /login
                #use_forward:       false
                failure_path:      /login

                oauth_user_provider:
                    service: messenger_user_provider

    access_control:
        - { path: ^/login, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/connect, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/, role: ROLE_USER }


Next edit routing.yml to add routes for the oauthentication and import the messenger routing file:

// app/config/routing.yml
hwi_oauth_redirect:
    resource: "@HWIOAuthBundle/Resources/config/routing/redirect.xml"
    prefix:   /connect

hwi_oauth_login:
    resource: "@HWIOAuthBundle/Resources/config/routing/login.xml"
    prefix:   /login

facebook_login:
    pattern: /login/check-facebook

bio_ware_interview_messenger:
    resource: "@BioWareInterviewMessengerBundle/Resources/config/routing.yml"
    prefix:   /


Finally, run the following commands in the console to initialize the database and everything should be ready to go:

$ php app/console doctrine:database:create
$ php app/console doctrine:schema:update --force



Usage
-------------

As a RESTful service, this bundle is used by making GET and POST requests to the bundle. The bundle then accesses the database and returns data in JSON format if applicable. In order to use the service, a user must first login through Facebook and obtain an OAuth token. On login, the users data is saved to the database as well as stored in the OAuth token for later use.

To login, access the HWI login route. By default on a local server this would be:
    http://localhost:8000/login

Once authenticated, a user may access the messenger functions of the bundle. For convenience, a logout route has also been provided. By default it is accessed at:
    http://localhost:8000/logout


Functions:

    Add Friend:
        Method: POST
        URI: /messenger/friendsList/{facebookId}
        Parameters:
            facebookId - the unique identifier provided by Facebook for the friend you wish to add
        Description: 
            Making a POST request with a valid Facebook ID will add that user to the logged in users friends list.
            In order to be valid, the friend must have previously logged in through the OAuthentication so that thier data is in the user database.
            If the friend is added successfully or is already a friend, a success response is returned.
            If the friend is not in the database or the user attempts to add themselves, a 400 Bad Request response is returned.
        Response Data Format:
            None

    Get Friends List
        Method: GET
        URI: /messenger/friendsList
        Parameters:
            None
        Description: 
            Making a GET request will return a list of the logged in users friends in JSON format.
        Response Data Format:
            [
                {
                    "facebookId":"123456789123456789",
                    "name":"John Doe"
                },
                {
                    "facebookId":"123456789123456788",
                    "name":"Jane Doe"
                },
                ...
            ]

    Add Message
        Method: POST
        URI: /messenger/messages/{facebookId}
        Parameters:
            facebookId - the unique identifier provided by Facebook for the friend you wish to message
            message - the message to be added, located in the POST data under "message=<Your message here>"
        Description: 
            Making a POST request with a valid Facebook ID and a message properly set in the POST data will add the message to the database.
            In order to be valid, the ID must be for someone that you have already added as a friend using Add Friend.
            If the message is added successfully, a success response is returned.
            If the message is empty or missing, the receipient is not in the database or the receipient is not a friend, a 400 Bad Request response is returned.
        Response Data Format:
            None

    Get Friend Messages
        Method: GET
        URI: /messenger/messages/{facebookId}
        Parameters:
            facebookId - the unique identifier provided by Facebook for the friend you wish to read messages from
        Description: 
            Making a GET request with a valid Facebook ID will return a list of the messages from the specified user.
            In order to be valid, the ID must be for someone that you have already added as a friend using Add Friend.
            If the messages are received successfully, a JSON response is returned with the messages ordered from newest to oldest.
            If the receipient is not in the database or the receipient is not a friend, a 400 Bad Request response is returned.
        Response Data Format:
            {
                "senderId":"123456789123456789",
                "senderName":"John Doe",
                "receipientId":"123456789123456788",
                "receipientName":"Jane Doe",
                "messages":[
                    {
                        "message":"Ezekiel 25:17",
                        "timeCreated":1405981723
                    },
                    {
                        "message":"by the inequities of the selfish and the tyranny of evil men",
                        "timeCreated":1405981697
                    },
                    {
                        "message":"The path of the righteous man is beset on all sides",
                        "timeCreated":1405920965
                    },
                    ...
                ]
            }