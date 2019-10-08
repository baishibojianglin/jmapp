<template>
	<view class="content">
		<vrow>
			<vcol span="100">
				<view class="contentcss"><u-parse :content="content.value" userSelect="none" /></view>				
			</vcol>	
		</vrow>
	</view>
</template>

<script>
	import vrow from '@/components/lml-layout/row.vue'
	import vcol from '@/components/lml-layout/col.vue'
	import uParse from '@/components/gaoyia-parse/parse.vue'


		
	export default {
		components:{
			vrow,vcol,uParse
		},
		data() {
			return {
		      content:{}
			}
		},
		onLoad(options) {
	        //加载文章详情内容
	         var self=this;
	         uni.request({
	        	url:self.$url+'index.php/index/Index/about',
	        	method:'GET',
	        	success:function(res){
	        		self.content=res.data;
	        	}
	         }) 
		},
		methods: {

		}
	}
</script>

<style>
	.content {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}
	.borbottom{
		background:-webkit-linear-gradient(left,#82858A,#695648); /* Safari 5.1 - 6.0 */
		background:linear-gradient(left,#82858A,#0F2822);/*chrom*/
		background:-ms-linear-gradient(left,#82858A,#695648);/* IE 10 */
		background:-moz-linear-gradient(left,#82858A,#695648);/*火狐*/ 
		background:-o-linear-gradient(left,#82858A,#695648); /* Opera 11.1 - 12.0 */
	}
	.center {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		text-align: center;
	}
	.fon25{
		font-size: 25upx;
		color:#fff;
	}
	.img {
		width:140upx;
		height:140upx;
		border-radius: 100%;
		margin: 15upx;
	}
	.colheight{
		line-height: 100upx;
		color:#fff;
		border-left:1px solid #E3E0D5;
	}
	.colheight-vrow{
		border-bottom: 1px solid #E3E0D5;
	}
	.designname{
		padding-bottom: 10upx;
	}
	.colright{
		border-right: 1px solid #E3E0D5;
	}
	.contentcss{
		padding: 0upx 10upx;
	}

</style>
