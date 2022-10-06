<template>
    <DefaultField
        :field="field"
        :errors="errors"
        :show-help-text="showHelpText"
    >
        <template #field>
            <div class="space-y-4">
                <GMapMap
                    :center="map.center"
                    :zoom="map.zoom"
                    style="height: 20rem; margin-top: 25px"
                >
                    <GMapMarker
                        :position="map.center"
                        @dragend="markerDragend"
                        :draggable="true"
                    />
                </GMapMap>
                <div class="flex">
                    <div class="w-1/2 mr-6" v-if="!hideLatitude">
                        <p class="mb-1">Latitude</p>
                        <input
                            type="text"
                            class="w-full form-control form-input form-input-bordered"
                            :class="errorClasses"
                            v-model="latitude"
                        />
                    </div>
                    <div class="w-1/2" v-if="!hideLongitude">
                        <p class="mb-1">Longitude</p>
                        <input
                            type="text"
                            class="w-full form-control form-input form-input-bordered"
                            :class="errorClasses"
                            v-model="longitude"
                        />
                    </div>
                </div>
            </div>
        </template>
    </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'

const map = { center: {}, selectedPlace: false }

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    data() {
        return {
            map,
            api_key: null,
            latitude: null,
            longitude: null,
            hideLatitude: false,
            hideLongitude: false
        }
    },

    mounted() {
        Nova.$on('latitude-update', data => {
            this.latitude = data
            this.map.center.lat = data
        })
        Nova.$on('longitude-update', data => {
            this.longitude = data
            this.map.center.lng = data
            this.map.zoom = 16
        })
    },

    methods: {
        setInitialValue() {
            if (this.field.hide_latitude) {
                this.hideLatitude = this.field.hide_latitude
            }
            if (this.field.hide_longitude) {
                this.hideLongitude = this.field.hide_longitude
            }
            this.map.center.lat = parseFloat(this.field.latitude)
            this.map.center.lng = parseFloat(this.field.longitude)
            this.latitude = parseFloat(this.field.latitude)
            this.longitude = parseFloat(this.field.longitude)
            this.map.zoom = this.field.zoom
            if (this.resourceId) {
                this.map.selectedPlace = true
                this.map.zoom = 16
            }
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute + '[latitude]', this.latitude)
            formData.append(
                this.field.attribute + '[longitude]',
                this.longitude
            )
        },

        markerDragend(marker) {
            let lat = marker.latLng.lat()
            let lng = marker.latLng.lng()

            fetch(
                `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${Nova.appConfig.api_key}`
            )
                .then(response => response.json())
                .then(data => this.setPlace(data.results[0], true))
        },

        setPlace(place, dragend = false) {
            for (const component of place.address_components) {
                const componentType = component.types[0]
                switch (componentType) {
                    case 'postal_code': {
                        Nova.$emit('zip-code-update', component.long_name)
                        break
                    }
                    case 'postal_code_suffix': {
                        Nova.$emit('zip-code-update', component.long_name)
                        break
                    }
                    case 'locality': {
                        Nova.$emit('city-update', component.long_name)
                        break
                    }
                    case 'administrative_area_level_1': {
                        Nova.$emit('state-update', component.long_name)
                        break
                    }
                    case 'country':
                        Nova.$emit('country-update', component.long_name)
                        break
                }
            }

            Nova.$emit('address-update', place.formatted_address)

            if (dragend) {
                this.latitude = place.geometry.location.lat
                this.longitude = place.geometry.location.lng
                this.map.center.lat = place.geometry.location.lat
                this.map.center.lng = place.geometry.location.lng
            } else {
                this.map.center.lat = place.geometry.location.lat()
                this.map.center.lng = place.geometry.location.lng()
                this.latitude = place.geometry.location.lat()
                this.longitude = place.geometry.location.lng()
            }
            this.map.zoom = 16
        }
    }
}
</script>
