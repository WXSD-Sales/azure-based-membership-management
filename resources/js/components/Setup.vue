<template>
    <section>
        <auth-hero />
        <div class="columns is-centered is-mobile my-1 py-6">
            <div class="column is-four-fifths">
                <!-- Email -->
                <div class="columns is-mobile mb-4 pb-4">
                    <div class="column is-2 has-text-info has-text-centered">
                        <h2 class="is-size-2 has-text-info">
                            1.
                        </h2>
                    </div>
                    <div class="column is-10">
                        <h2 class="is-size-2">
                            Your Email
                        </h2>
                        <p class="subtitle">
                            Provide your email address.
                        </p>
                        <p class="block">
                            This email address shall be used for OAuth with Azure and Webex.
                        </p>
                        <form
                            class="columns"
                            method="POST"
                            :action="url['email']"
                        >
                            <input
                                type="hidden"
                                name="_token"
                                :value="csrf"
                            >
                            <div class="column">
                                <b-field
                                    label="Email"
                                    label-position="on-border"
                                    custom-class="is-large"
                                    :type="error['email'] ? 'is-danger': email ? 'is-success' : ''"
                                    :message="error['email'] ? error['email'][0] + ' Kindly retry.' : ''"
                                >
                                    <b-input
                                        placeholder="john@example.com"
                                        size="is-large"
                                        icon="email"
                                        name="email"
                                        type="email"
                                        custom-class="is-rounded"
                                        :value="email"
                                        :disabled="isEmailSet"
                                        required
                                    />
                                </b-field>
                            </div>
                            <div
                                v-if="!email || error['email']"
                                class="column is-4"
                            >
                                <b-button
                                    type="is-link"
                                    native-type="submit"
                                    size="is-large"
                                    icon-right="chevron-right"
                                    class="is-rounded"
                                    expanded
                                >
                                    Next
                                </b-button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Azure -->
                <div
                    v-if="isEmailSet"
                    class="columns is-mobile mb-4 pb-4"
                >
                    <div class="column is-2 has-text-info has-text-centered">
                        <h2 class="is-size-2">
                            2.
                        </h2>
                    </div>
                    <div class="column is-10">
                        <h2 class="is-size-2">
                            Azure OAuth
                        </h2>
                        <p class="subtitle">
                            Authenticate with Microsoft Azure.
                        </p>
                        <p
                            v-if="!azure && !error['azure']"
                            class="block"
                        >
                            Click the button to authorize this application on Azure.
                        </p>
                        <p
                            v-if="error['azure']"
                            class="block has-text-danger"
                        >
                            {{ error['azure'][0] }} Kindly retry.
                        </p>
                        <div class="columns">
                            <div class="column">
                                <b-field
                                    v-if="azure"
                                    label="Azure User Id"
                                    label-position="on-border"
                                    custom-class="is-large"
                                    type="is-success"
                                >
                                    <b-input
                                        size="is-large"
                                        type="password"
                                        custom-class="is-rounded"
                                        :value="azure"
                                        disabled
                                        readonly
                                    />
                                </b-field>
                                <a
                                    v-else
                                    class="button is-large is-fullwidth is-link is-rounded"
                                    :href="url['azure']"
                                >
                                    <img
                                        :src="'/images/microsoft.svg'"
                                        width="40"
                                        alt=""
                                    >
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Webex -->
                <div
                    v-if="isEmailSet && isAzureSet"
                    class="columns is-mobile mb-4 pb-4"
                >
                    <div class="column is-2 has-text-info has-text-centered">
                        <h2 class="is-size-2 has-text-info">
                            3.
                        </h2>
                    </div>
                    <div class="column is-10">
                        <h2 class="is-size-2">
                            Webex OAuth
                        </h2>
                        <p class="subtitle">
                            Authenticate with Cisco Webex.
                        </p>
                        <p
                            v-if="!webex && !error['webex']"
                            class="block"
                        >
                            Click the button to authorize this application on Webex.
                        </p>
                        <p
                            v-if="error['webex']"
                            class="block has-text-danger"
                        >
                            {{ error['webex'][0] }} Kindly retry.
                        </p>
                        <div class="columns">
                            <div class="column">
                                <b-field
                                    v-if="webex"
                                    label="Webex User Id"
                                    label-position="on-border"
                                    custom-class="is-large"
                                    type="is-success"
                                >
                                    <b-input
                                        size="is-large"
                                        type="password"
                                        custom-class="is-rounded"
                                        :value="webex"
                                        disabled
                                        readonly
                                    />
                                </b-field>
                                <a
                                    v-else
                                    class="button is-large is-fullwidth is-link is-rounded"
                                    :href="url['webex']"
                                >
                                    <img
                                        :src="'/images/cisco.svg'"
                                        width="60"
                                        alt=""
                                    >
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    v-if="isEmailSet"
                    class="columns my-4 py-4"
                >
                    <form
                        method="POST"
                        :action="url['reset']"
                        class="column"
                    >
                        <input
                            type="hidden"
                            name="_token"
                            :value="csrf"
                        >
                        <b-button
                            type="is-danger"
                            native-type="submit"
                            size="is-large"
                            icon-left="restore"
                            class="is-rounded"
                            expanded
                        >
                            Reset
                        </b-button>
                    </form>
                    <form
                        v-if="isEmailSet && isAzureSet && isWebexSet"
                        method="POST"
                        :action="url['setup']"
                        class="column is-8"
                    >
                        <input
                            type="hidden"
                            name="_token"
                            :value="csrf"
                        >
                        <b-button
                            type="is-link"
                            native-type="submit"
                            size="is-large"
                            icon-right="chevron-right"
                            class="is-rounded"
                            expanded
                        >
                            Finish
                        </b-button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import AuthHero from './common/AuthHero'

export default {
    name: 'Setup',
    components: {
        AuthHero
    },
    props: {
        csrf: {
            type: String,
            required: true
        },
        email: {
            type: String,
            default: null
        },
        azure: {
            type: String,
            default: null
        },
        webex: {
            type: String,
            default: null
        },
        error: {
            type: Object,
            required: true
        },
        url: {
            type: Object,
            required: true
        }
    },
    computed: {
        isEmailSet: function () {
            return this.email.length > 0 && !('email' in this.error)
        },
        isAzureSet: function () {
            return this.azure.length > 0 && !('azure' in this.error)
        },
        isWebexSet: function () {
            return this.webex.length > 0 && !('webex' in this.error)
        }
    }
}
</script>

<style scoped>
</style>
