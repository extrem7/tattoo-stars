<template>
    <div class="form-group">
        <a :href="route(`${resource}.create`)" class="btn btn-success">Создать</a>
    </div>
</template>
<script>
    export default {
        inject: ['resource']
    }
</script>
