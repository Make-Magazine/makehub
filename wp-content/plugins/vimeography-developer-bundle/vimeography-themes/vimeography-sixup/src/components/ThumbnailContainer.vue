<script>
  import Thumbnail from './Thumbnail.vue';

  const template = `
    <div class="vimeography-thumbnail-container">
      <thumbnail
        v-for="(video, index) in videos"
        v-bind:video="video"
        v-bind:index="index"
        v-bind:key="video.id">
      </thumbnail>
    </div>
  `;

  const ThumbnailContainer = {
    props: ['videos'],
    template,
    components: {
      Thumbnail
    }
  }

  export default ThumbnailContainer;
</script>

<style lang="scss" scoped>
  .vimeography-thumbnail-container {
    position: relative;

    display: grid;
    grid-template-columns: 50% 50%;
    grid-column-gap: 10px;
    align-content: start;
    grid-row-gap: 10px;
  }
</style>
