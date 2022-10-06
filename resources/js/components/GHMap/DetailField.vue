<template>
    <div
        class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0"
        :class="{
            'border-t border-gray-100 dark:border-gray-700': index !== 0
        }"
        :dusk="field.attribute"
    >
        <div class="md:w-1/4 md:py-3">
            <slot>
                <h4 class="font-bold md:font-normal">
                    <span>{{ this.field.name }}</span>
                </h4>
            </slot>
        </div>
        <div class="md:w-3/4 md:py-3 break-words">
            <slot name="value">
                <span>{{ field.latitude + ', ' + field.longitude }}</span>
                <GMapMap
                    :center="map.center"
                    :zoom="map.zoom"
                    style="height: 20rem; margin-top: 25px"
                >
                    <GMapMarker :position="map.center" :draggable="false" />
                </GMapMap>
            </slot>
        </div>
    </div>
</template>

<script>
const map = { center: {}, selectedPlace: false }

export default {
    props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

    data() {
        return {
            map,
            api_key: null,
            latitude: null,
            longitude: null
        }
    },

    mounted() {
        this.map.center.lat = parseFloat(this.field.latitude)
        this.map.center.lng = parseFloat(this.field.longitude)
        this.latitude = parseFloat(this.field.latitude)
        this.longitude = parseFloat(this.field.longitude)
        this.map.zoom = 16
    }
}
</script>
