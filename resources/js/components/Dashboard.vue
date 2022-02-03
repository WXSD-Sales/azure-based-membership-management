<template>
    <section>
        <auth-hero />
        <div class="columns is-centered is-multiline is-mobile my-1 py-6">
            <div class="column is-four-fifths">
                <div class="columns is-vcentered">
                    <div class="column is-three-fifths has-text-centered-mobile is-subtitle">
                        <p>{{ syncStatus }}</p>
                    </div>
                    <div class="column is-two-fifths">
                        <b-button
                            expanded
                            :loading="isButtonLoading"
                            :disabled="isButtonLoading || isTableLoading"
                            size="is-medium"
                            label="Sync Now"
                            type="is-link"
                            icon-right="sync"
                            class="is-rounded"
                            @click="performCrossSync"
                        />
                    </div>
                </div>
            </div>
            <div class="column is-four-fifths">
                <b-table
                    :columns="columns"
                    :data="syncMappings"
                    :default-sort="['id']"
                    :loading="isTableLoading"
                    :height="900"
                    hoverable
                    sticky-header
                    detailed
                    detail-key="id"
                >
                    <b-table-column
                        v-slot="props"
                        field="id"
                        label="ID"
                        numeric
                        searchable
                        sortable
                        width="160"
                    >
                        {{ props.row.id }}
                    </b-table-column>
                    <b-table-column
                        v-slot="props"
                        field="azureGroupId"
                        label="Azure Group Id"
                        searchable
                        sortable
                    >
                        <b-field>
                            <b-input
                                type="search"
                                :value="props.row.azureGroupId"
                                size="is-small"
                                rounded
                                disabled
                            />
                            <p class="control">
                                <b-button
                                    type="is-link is-light"
                                    icon-right="content-copy"
                                    size="is-small"
                                    rounded
                                    @click="copyToClipBoard(props.row.azureGroupId)"
                                />
                            </p>
                        </b-field>
                    </b-table-column>
                    <b-table-column
                        v-slot="props"
                        field="webexGroupId"
                        label="Webex Group Id"
                        searchable
                        sortable
                    >
                        <b-field>
                            <b-input
                                type="search"
                                :value="props.row.webexGroupId"
                                size="is-small"
                                rounded
                                disabled
                            />
                            <p class="control">
                                <b-button
                                    type="is-link is-light"
                                    icon-right="content-copy"
                                    size="is-small"
                                    rounded
                                    @click="copyToClipBoard(props.row.webexGroupId)"
                                />
                            </p>
                        </b-field>
                    </b-table-column>
                    <b-table-column
                        v-slot="props"
                        field="azureGroupName"
                        label="Azure Group Name"
                        searchable
                        sortable
                    >
                        <div :title="props.row.azureGroupName">
                            {{ props.row.azureGroupName }}
                        </div>
                    </b-table-column>
                    <b-table-column
                        v-slot="props"
                        field="webexGroupName"
                        label="Webex Group Name"
                        searchable
                        sortable
                    >
                        <div :title="props.row.webexGroupName">
                            {{ props.row.webexGroupName }}
                        </div>
                    </b-table-column>
                    <b-table-column
                        v-slot="props"
                        label="Details"
                        width="50"
                        centered
                    >
                        <b-tooltip
                            type="is-light"
                            :triggers="['click']"
                            :auto-close="['outside', 'escape']"
                            position="is-left"
                            multilined
                        >
                            <template #content>
                                {{ 'updated on ' + new Date(props.row.updatedAt) }}
                            </template>
                            <b-button
                                icon-right="information"
                                class="is-rounded"
                                type="is-link is-light"
                                size="is-small"
                            />
                        </b-tooltip>
                    </b-table-column>
                    <template #detail="props">
                        <b-field
                            :label="'Azure Group Members (' + (props.row.azureGroupUsers.length) + ')'"
                            label-position="on-border"
                        >
                            <b-taginput
                                v-model="props.row.azureGroupUsers"
                                :closable="false"
                                class="is-rounded"
                                type="is-link is-light"
                                disabled
                                rounded
                            />
                        </b-field>
                        <b-field
                            :label="'Webex Group Members ('+ (props.row.webexGroupUsers.length) + ')'"
                            label-position="on-border"
                        >
                            <b-taginput
                                v-model="props.row.webexGroupUsers"
                                :closable="false"
                                type="is-link is-light"
                                disabled
                                rounded
                            />
                        </b-field>
                    </template>
                </b-table>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'Dashboard',
    data () {
        return {
            syncMappings: [],
            columns: [],
            isTableLoading: true,
            isButtonLoading: false,
            syncStatus: ''
        }
    },
    created () {
        this.loadTable()
    },
    methods: {
        copyToClipBoard (content) {
            navigator.clipboard.writeText(content)
            this.$buefy.toast.open({
                duration: 1000,
                message: 'Copied contents to clipboard!',
                position: 'is-top',
                type: 'is-success'
            })
        },
        async retrieveAzureMemberships () {
            const [retrieveAzureUsers, retrieveAzureGroups] = await Promise.all([
                window.axios.get('/retrieveAzureUsers'),
                window.axios.get('/retrieveAzureGroups')
            ])

            return retrieveAzureUsers.status === 200 && retrieveAzureGroups.status === 200
        },
        async retrieveWebexMemberships () {
            const [retrieveWebexUsers, retrieveWebexGroups] = await Promise.all([
                window.axios.get('/retrieveWebexUsers'),
                window.axios.get('/retrieveWebexGroups')
            ])

            return retrieveWebexUsers.status === 200 && retrieveWebexGroups.status === 200
        },
        async performCrossSync () {
            this.isButtonLoading = true

            this.syncStatus = 'Retrieving Azure memberships...'
            if (!(await this.retrieveAzureMemberships())) {
                this.syncStatus = 'Failed to retrieve Azure memberships.'
                this.isButtonLoading = false
                return
            }

            this.syncStatus = 'Retrieving Webex memberships...'
            if (!(await this.retrieveWebexMemberships())) {
                this.syncStatus = 'Failed to retrieve Webex memberships.'
                this.isButtonLoading = false
                return
            }

            this.syncStatus = 'Performing cross sync...'
            window.axios
                .get('/performCrossSync')
                .then(() => {
                    this.syncStatus = `Last sync completed on ${new Date()}.`
                })
                .catch(() => {
                    this.syncStatus = 'Failed to cross sync memberships.'
                })
                .finally(() => {
                    this.isButtonLoading = false
                    this.loadTable()
                })
        },
        loadTable () {
            this.isTableLoading = true
            window.axios.get('/memberships')
                .then(response => {
                    function getSyncMapping (mapping) {
                        return {
                            id: parseInt(mapping.id),
                            azureGroupId: mapping.azure_group_id,
                            azureGroupName: mapping.azure_group.name,
                            azureGroupUsers: mapping.azure_group.users.map(x => x.email).sort(),
                            webexGroupId: mapping.webex_group_id,
                            webexGroupName: mapping.webex_group.name,
                            webexGroupUsers: mapping.webex_group.users.map(x => x.email).sort(),
                            createdAt: mapping.created_at,
                            updatedAt: mapping.updated_at
                        }
                    }

                    this.syncMappings = response.data.map(o => getSyncMapping(o))
                })
                .catch(error => {
                    console.error(error)
                    this.$buefy.toast.open({
                        duration: 5000,
                        message: `${error}. You may retry after sometime.`,
                        position: 'is-top',
                        type: 'is-danger'
                    })
                })
                .finally(() => {
                    this.isTableLoading = false
                })
        }
    }
}
</script>

<style scoped>

</style>
