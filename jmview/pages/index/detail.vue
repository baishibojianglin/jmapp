<template>
	<view class="content">
		<vrow class="borbottom">
		    <vcol span="30" class="center fon25">
               <vrow>
				   <image class="img" :src="content.avatar"></image>
			   </vrow>
			   <vrow class="designname">设计师：{{content.member_name}}</vrow>
		    </vcol>	
		    <vcol span="68" class="center fon25 colheight">
                <vrow class="colheight-vrow">
					<vcol span="22" class="colright">风格</vcol>
					<vcol span="22" class="colright">大小</vcol>
					<vcol span="22">造价</vcol>
				</vrow>
				<vrow>
					<vcol span="22" class="colright">{{content.keywords}}</vcol>
					<vcol span="22" class="colright">{{content.area}}</vcol>
					<vcol span="22">{{content.price}}</vcol>
				</vrow>
		    </vcol>	
		</vrow>
		<vrow>
			<vcol span="100">
				<view class="contentcss"><u-parse :content="content.content" userSelect="none" /></view>				
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
	        	url:self.$url+'index.php/index/Index/detail',
				data:{id:options.titleid},
	        	method:'POST',
				header:{'content-type':'application/x-www-form-urlencoded'},
	        	success:function(res){
					res.data.avatar=self.$url+res.data.avatar.replace(/\\/g, "/");//拼接处理图片路径
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
