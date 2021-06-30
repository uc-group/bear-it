<template>
    <v-dialog v-model="showDialog" max-width="600px">
        <v-card>
            <v-card-title class="text-h5">{{ message }}</v-card-title>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="cancel">Cancel</v-btn>
                <v-btn color="blue darken-1" text @click="confirm">OK</v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: "ConfirmDialog",
    props: {
        showDialog: Boolean,
        message: String,
        cancelLabel: {
            type: String,
            default: 'Cancel'
        },
        confirmLabel: {
            type: String,
            default: 'OK'
        }
    },
    methods: {
        confirm() {
            this.$emit('confirm')
        },
        cancel() {
            this.$emit('cancel')
        }
    }
}
</script>
