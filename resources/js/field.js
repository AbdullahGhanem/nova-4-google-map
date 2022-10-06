import TRMapIndexField from './components/GHMap/IndexField'
import TRMapDetailField from './components/GHMap/DetailField'
import TRMapFormField from './components/GHMap/FormField'


import VueGoogleMaps from '@fawmi/vue-google-maps'

Nova.booting((app, store) => {
    app.use(VueGoogleMaps, {
        load: {
            key: Nova.appConfig.api_key,
            libraries: 'places'
        }
    })
    app.component('index-gh-map', TRMapIndexField)
    app.component('detail-gh-map', TRMapDetailField)
    app.component('form-gh-map', TRMapFormField)
})