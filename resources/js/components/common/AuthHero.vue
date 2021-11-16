<template>
    <div class="hero is-medium is-link">
        <div class="hero-body">
            <h1 class="title is-size-1">
                {{ $route.path.toLowerCase() === '/setup' ? 'Setup ' : '' }}Azure to Webex Group Sync
            </h1>
            <p class="subtitle">
                Sync your Microsoft Azure Active Directory groups to Cisco Webex teams.
            </p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'AuthHero'
}
</script>

<style scoped>

</style>
