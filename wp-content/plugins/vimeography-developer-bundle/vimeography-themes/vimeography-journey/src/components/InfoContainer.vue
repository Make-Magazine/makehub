<script>
import { mapState } from 'vuex'
import { DownloadLink } from 'vimeography-blueprint'

const defaultTemplate = `
  <div class="vimeography-info">
    <div class="vimeography-scroller">
      <h2 class="vimeography-title">{{activeVideo.name}}</h2>
      <span class="vimeography-duration">{{activeVideo.duration}}</span>
      <div class="vimeography-description" v-html="activeVideo.description"></div>
      <download-link :video="activeVideo"></download-link>
    </div>
  </div>
`;

const userTemplate = document.querySelector('#vimeography-journey-info');

const InfoContainer = {
  props: ['activeVideo'],
  components: {
    DownloadLink
  },
  template: userTemplate ? userTemplate.innerText : defaultTemplate,
}

export default InfoContainer;
</script>

<style lang="scss" scoped>
  .vimeography-info {
    position: relative;

    width: 100%;
    height: auto;
    background: #232323;
    color: #ccc;
    overflow: hidden;
    border-radius: 2px;
    margin-bottom: 1rem;

    &:after {
      content: '';
      position: absolute;
      bottom: 0;
      width: 100%;
      height: 25px;
      background: linear-gradient(rgba(255, 255, 255, 0.001), #232323); /* transparent keyword is broken in Safari */
    }
  }

  .vimeography-scroller {
    overflow-y: scroll;
    width: 100%;
    height: 260px;
    padding: 15px 0;
    line-height: 1.2rem;
  }

  .vimeography-title {
    padding: 0 15px 10px;
    font-size: 18px;
    line-height: 20px;
    margin: 0;
    color: #f4f4f4;
    text-transform: uppercase;
  }

  .vimeography-duration {
    padding: 0 15px 15px;
    margin: 0;
    color: #f4f4f4;
    text-transform: uppercase;
    font-size: 14px;
    line-height: 14px;
    font-weight: normal;
  }

  .vimeography-description {
    padding: 0 15px 35px;
    margin: 0;
    color: #f4f4f4;
    font-size: 12px;
    line-height: 1.4em;
    overflow: hidden;
  }

  /deep/ .vimeography-download {
    padding: 0 15px 35px;
    margin: 0;
  }

  @media screen and (min-width: 760px) {
    .vimeography-info {
      height: auto;
    }

    .vimeography-scroller {
      position: absolute;
      height: 100%;
      top: 0;
      right: 0;
      bottom: 0;
    }
  }
</style>
