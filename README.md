Azure based Membership Management
=================================
**Auto manage Team and Space memberships on Webex with Azure AD Groups.**

This is a proof-of-concept application that automatically links [Azure Active Directory group memberships](https://docs.microsoft.com/en-us/azure/active-directory/fundamentals/active-directory-groups-create-azure-portal) to corresponding [team memberships on Webex](https://developer.webex.com/docs/api/v1/teams) by creating those teams and then adding Webex users to them. For example:
> Your organization has a group on Azure AD named `chicago-employees` for all subset of employees that are based in the city of Chicago. This PoC will automatically create a corresponding `chicago-employees` team on Webex and then add those employees to the team. As a result, Chicago based employees can collaborate seamlessly on Webex by creating [Spaces](https://help.webex.com/en-US/article/hk71r4/Webex-App-%7C-Create-a-space) without having an admin or group owner to manually add or remove members using their email id.

The target audience for this PoC are IT Administrators or group owners who want an effortless way to manage team memberships on Webex across their organization. The PoC supports [Dynamic and Assigned/Static Azure AD memberships](https://docs.microsoft.com/en-us/azure/active-directory/fundamentals/active-directory-groups-create-azure-portal#membership-types) as well as [Security and O365 Azure AD groups](https://docs.microsoft.com/en-us/azure/active-directory/fundamentals/active-directory-groups-create-azure-portal#group-types). It can further be customized to support Webex User account provisioning, etc.

<p align="center">
   <a href="https://www.youtube.com/watch?v=lKNUpkCK6uI&t=87s" target="_blank">
       <img src="https://user-images.githubusercontent.com/6129517/144125345-dda6e239-a271-478e-ac41-ac28d74832a6.gif" alt="azure-group-sync-demo"/>
    </a>
</p>

<!-- ⛔️ MD-MAGIC-EXAMPLE:START (TOC:collapse=true&collapseText=Click to expand) -->
<details>
<summary>Table of Contents (click to expand)</summary>
    
  * [Overview](#overview)
  * [Setup](#setup)
  * [Demo](#demo)
  * [Support](#support)

</details>
<!-- ⛔️ MD-MAGIC-EXAMPLE:END -->

## Overview
At it's core, the application is a collection of background processes that run on a predefined schedule.

These processes, collectively, retrieve and compare membership details across the two platforms; treating AD groups as the source. 

Finally, the application utilizes a Webex Bot account to create, update or delete teams on Webex, as required. Of course, this is an over-simplification of the steps involved. For example, syncing large orgs with thousands of users can be particularly time-consuming. However, this POC can be modified to account for many such scenarios.

## Setup

These instructions assume that you have:
 - Administrator access to an Azure AD Tenant and Webex Control Hub.
 - Configured the SCIM based connector to automatically provision and de-provision users to Webex. Future versions of the project may not need this, but for now, please complete either of these tutorials first:
   - [Tutorial: Configure Cisco Webex for automatic user provisioning](https://docs.microsoft.com/en-us/azure/active-directory/saas-apps/cisco-webex-provisioning-tutorial)
   - [Synchronize Azure Active Directory Users into Control Hub](https://help.webex.com/en-US/article/6ta3gz/Synchronize-Azure-Active-Directory-Users-into-Control-Hub)
 - [Docker installed](https://docs.docker.com/engine/install/) and running on a Windows (via WSL2), macOS, or Linux machine.

Open a new terminal window and follow the instructions below to setup the project locally for
development/demo.

1. Clone this repository and change directory:
   ```
   git clone https://github.com/WXSD-Sales/azure-based-membership-management && cd azure-based-membership-management
   ```
   
2. Rename `.env.local` file to `.env` (you may also change the database credentials within this renamed file):
   ```
   mv .env.local .env
   ```
   
3. Review and follow the [Quickstart: Register an application with the Microsoft identity platform](https://docs.microsoft.com/en-us/azure/active-directory/develop/quickstart-register-app#register-an-application) guide.
   - Select the following [Microsoft Graph API permissions](https://docs.microsoft.com/en-us/azure/active-directory/develop/quickstart-configure-app-access-web-apis#delegated-permission-to-microsoft-graph):
      | API / Permissions name | Type      | Description                                         |
      |------------------------|-----------|-----------------------------------------------------|
      | Directory.Read.All     | Delegated | Read directory data                                 |
      | email                  | Delegated | View users' email address                           |
      | Group.Read.All         | Delegated | Read all groups                                     |
      | GroupMember.Read.All   | Delegated | Read group memberships                              |
      | offline_access         | Delegated | Maintain access to data you have given it access to |
      | openid                 | Delegated | Sign users in                                       |
      | profile                | Delegated | View users' basic profile                           |
      | User.Read              | Delegated | Sign in and read user profile                       |
      | User.Read.All          | Delegated | Read all users' full profiles                       |
   - Use these [Redirect URIs](https://docs.microsoft.com/en-us/azure/active-directory/develop/quickstart-register-app#add-a-redirect-uri):
     - `https://localhost/auth/azure/callback` 
     - `http://localhost/auth/azure/callback`
   - Take note of your [Azure Tenant ID](https://docs.microsoft.com/en-us/azure/active-directory/fundamentals/active-directory-how-to-find-tenant), Application ID and, Client Secret. Assign these values to the `AZURE_TENANT_ID`, `AZURE_CLIENT_ID`, and `AZURE_CLIENT_SECRET` environment variables within the `.env` file respectively.

4. Review and follow the [Registering your Integration
 on Webex](https://developer.webex.com/docs/integrations#registering-your-integration) guide.
   - Your registration must have the following [Webex REST API scopes](https://developer.webex.com/docs/integrations#scopes):
      | Scope                   | Description                                   |
      |-------------------------|-----------------------------------------------|
      | spark-admin:people_read | Access to read your user's company directory  |
      | spark:kms               | Permission to interact with encrypted content |
   - Use these Redirect URIs: 
     - `https://localhost/auth/webex/callback`
     - `http://localhost/auth/webex/callback`
   - Take note of your Client ID and Client Secret. Assign these values to the `WEBEX_CLIENT_ID` and `WEBEX_CLIENT_SECRET` environment variables within the `.env` file respectively.

5. Review and follow the [Creating a Webex Bot](https://developer.webex.com/docs/bots#creating-a-webex-bot) guide. Take note of your Bot ID and Bot access token. Assign these values to the `WEBEX_BOT_ID` and `WEBEX_BOT_TOKEN` environment variables within the `.env` file respectively.

6. [Install Composer dependencies for the application](https://laravel.com/docs/9.x/sail#installing-composer-dependencies-for-existing-projects):
   ```
   docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
   ```

7. Start the Docker development environment via [Laravel Sail](https://laravel.com/docs/9.x/sail):
   ```
   ./vendor/bin/sail up -d
   ```

8. Generate the [application key](https://laravel.com/docs/9.x/encryption#configuration):
   ```
   ./vendor/bin/sail php artisan key:generate
   ```

9. Initialize the [database for the application](https://laravel.com/docs/9.x/migrations#drop-all-tables-migrate=):
   ```
   ./vendor/bin/sail php artisan migrate:fresh
   ```

10. Install NPM dependencies for the application:
    ```
    ./vendor/bin/sail npm install
    ```

11. Run [Laravel Mix](https://laravel.com/docs/9.x/mix):
    ```
    ./vendor/bin/sail npm run dev
    ```

Lastly, navigate to `http://localhost` in your browser to complete the setup (you will be asked to login to Azure and Webex). To exit, execute `./vendor/bin/sail down` on the termainal. To exit, execute `./vendor/bin/sail down` on the termainal.


## Demo

A video where I demo this PoC is available on YouTube — https://www.youtube.com/watch?v=lKNUpkCK6uI&t=87s.


## Support

Please reach out to the WXSD team at [wxsd@external.cisco.com](mailto:wxsd@external.cisco.com?cc=ashessin@cisco.com&subject=Azure%20Group%20Sync) or contact me on Webex (ashessin@cisco.com).
