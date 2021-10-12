<template>
    <section>
        <auth-hero />
        <div class="columns is-centered is-mobile my-1 py-6">
            <div class="column is-four-fifths">
                <b-table
                    :columns="columns"
                    :data="syncMappings"
                    :default-sort="['id']"
                    :loading="tableIsLoading"
                    :height="1024"
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
                        <div :title="props.row.azureGroupId">
                            {{ props.row.azureGroupId.slice(-7) }}
                        </div>
                    </b-table-column>
                    <b-table-column
                        v-slot="props"
                        field="webexGroupId"
                        label="Webex Group Id"
                        searchable
                        sortable
                    >
                        <div :title="props.row.webexGroupId">
                            {{ props.row.webexGroupId.slice(-7) }}
                        </div>
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
                                label=""
                                type="is-link is-light"
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
                                type="is-link is-light"
                                disabled
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
            tableIsLoading: true
        }
    },
    created () {
        window.axios
            .get('/sync-mappings')
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
                this.tableIsLoading = false
            })
    },
    methods: {}
}
</script>

<style scoped>

</style>
