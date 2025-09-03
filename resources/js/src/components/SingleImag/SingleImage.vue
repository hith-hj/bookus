<template>
  <div style="width:50px; margin:5px;">
    <div
      v-if="previewImage == null"
      class="imagePreviewWrapper"
      :style="{ 'background-image': `url(${ImageFile})` }"
      @click="selectImage"
    />
    <div
      v-else
      class="imagePreviewWrapper"
      :style="{ 'background-image': `url(${previewImage })` }"
      @click="selectImage"
    />
    <input
      ref="fileInput"
      type="file"
      @input="pickFile"
    >
  </div>

</template>
<script>
import { BButton } from 'bootstrap-vue'

export default {
  components: { BButton },
  props: {
    ImageFile: {
      type: File,
      default: null,
    },
  },
  data() {
    return {
      previewImage: null,
    }
  },
  methods: {
    selectImage() {
      this.$refs.fileInput.click()
    },
    pickFile() {
      const input = this.$refs.fileInput
      const file = input.files
      if (file && file[0]) {
        const reader = new FileReader()
        reader.onload = e => {
          this.previewImage = e.target.result
        }
        reader.readAsDataURL(file[0])
        this.$emit('input', file[0])
        this.$emit('update:ImageFile', file[0])
      }
    }
    ,

  },
}
</script>
<style scoped lang="scss">
.imagePreviewWrapper {
    width: 100px;
    height: 100px;
    display: block;
    cursor: pointer;
    border-radius: 25px;
    border: 2px solid rgb(112, 93, 138);
    margin: 0 auto 30px;
    background-size: cover;
    background-position: center center;
}
</style>
