<template>
    <span>{{ field.latitude + ', ' + field.longitude }}</span>
</template>

<script>
export default {
    props: ['resourceName', 'field']
}
</script>
