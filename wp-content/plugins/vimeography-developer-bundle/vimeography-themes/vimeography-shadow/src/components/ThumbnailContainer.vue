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

<style scoped>
  .vimeography-thumbnail-container {
    position: relative;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    grid-column-gap: 10px;
    grid-row-gap: 50px;
    justify-content: center;
    margin-bottom: 2rem;
  }

  /* IE11 Support */
  @media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
    .vimeography-thumbnail-container {
      display: flex;
      flex-wrap: wrap;
    }
  }
</style>
