Azure Group Sync
================
**Synchronize your Microsoft Azure AD groups to Cisco Webex.**

This is a a proof-of-concept application that automatically syncs Azure Active Directory groups to Webex.
The target audience for this PoC are IT Administrators or group owners who want an effortless way to manage team memberships on Webex across their organization.

<p align="center">
   <img src="https://user-images.githubusercontent.com/6129517/144125345-dda6e239-a271-478e-ac41-ac28d74832a6.gif" alt="azure-group-sync-demo"/>
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

Then open and new terminal window and follow the instructions below.

1. Clone this repository and change directory:
   ```
   git clone https://github.com/WXSD-Sales/azure-group-sync && cd azure-group-sync
   ```
   
2. Rename `.env.example` file to `.env` (you may also edit your database credentials within this renamed file):
   ```
   mv .env.example .env
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

6. Start the Docker development environment via [Laravel Sail](https://laravel.com/docs/8.x/sail):
   ```
   ./vendor/bin/sail up -d
   ```

7. Run [Laravel Mix](https://laravel.com/docs/8.x/mix)  
   When you run this command, the application's CSS and JavaScript assets will be compiled and placed in the application's public directory:
   ```
   ./vendor/bin/sail npm run dev
   ```

8. Run the Scheduler locally  
   This command will run in the foreground and invoke the scheduler every minute until you terminate the command:
   ```
   ./vendor/bin/sail php artisan schedule:work
   ```

9. Run the Queue Worker  
   Start a queue worker and process new jobs as they are pushed onto the queue. This command will continue to run until it is manually stopped or you close your terminal:
   ```
   ./vendor/bin/sail php artisan queue:work
   ```

Lastly, navigate to `http://localhost` in your browser to complete the setup (you will be asked to login to Azure and Webex).


## Demo

A video where I demo this PoC is available on YouTube — https://www.youtube.com/watch?v=lKNUpkCK6uI&t=87s.


## Support

Please reach out to the WXSD team at [wxsd@external.cisco.com](mailto:wxsd@external.cisco.com?cc=ashessin@cisco.com&subject=Azure%20Group%20Sync).
