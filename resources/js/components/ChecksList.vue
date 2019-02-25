<template id="checks-list">
    <div class="table-responsive">
        <table class="table table-hover table-light table-striped">
            <thead>
            <tr>
                <th>Enabled</th>
                <th>Status</th>
                <th>UUID</th>
                <th>Last check</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="check in filteredChecks" :if="check._source !== null" :key="check._id">
                <td><span class="badge"
                          :class="{'badge-danger': check._source.enabled === false, 'badge-success': check._source.enabled === true}">{{ check._source.enabled }}</span>
                </td>
                <td><span class="badge"
                          :class="{'badge-danger': check._source.status === 'FAIL', 'badge-success': check._source.status === 'OK'}">{{ check._source.status }}</span>
                </td>
                <td>{{ check._source.uuid }}</td>
                <td>{{ check._source.last_check }}</td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="4">
                    Status :
                    <button class="btn badge badge-light" :class="{'btn-outline-secondary': filters.status === null}"
                            @click.prevent="filters.status = null">ALL
                    </button>
                    <button class="btn badge badge-danger" :class="{'btn-outline-secondary': filters.status === 'FAIL'}"
                            @click.prevent="filters.status = 'FAIL'">FAIL
                    </button>
                    <button class="btn badge badge-success" :class="{'btn-outline-secondary': filters.status === 'OK'}"
                            @click.prevent="filters.status = 'OK'">OK
                    </button>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    Enabled :
                    <button class="btn badge badge-light" :class="{'btn-outline-secondary': filters.enabled === null}"
                            @click.prevent="filters.enabled = null">ALL
                    </button>
                    <button class="btn badge badge-danger" :class="{'btn-outline-secondary': filters.enabled === false}"
                            @click.prevent="filters.enabled = false">NO
                    </button>
                    <button class="btn badge badge-success" :class="{'btn-outline-secondary': filters.enabled === true}"
                            @click.prevent="filters.enabled = true">YES
                    </button>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                checks: null,
                filters: {
                    status: null,
                    enabled: null,
                },
            }
        },
        mounted() {
            this.$http.get('/api/checks/all').then(response => {
                this.checks = response.body.data;
            }, response => {
            })
        },
        computed: {
            filteredChecks() {
                let filtered = this.checks;

                if (this.filters.status !== null)
                    filtered = filtered.filter(check => check._source.status === this.filters.status);

                if (this.filters.enabled !== null)
                    filtered = filtered.filter(check => check._source.enabled === this.filters.enabled);
                return filtered;
            }
        }
    }
</script>