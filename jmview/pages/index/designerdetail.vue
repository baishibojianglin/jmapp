<template>
	<view class="content">
		<vrow class="borbottom">
		    <vcol span="100">
               <vrow class="content">
				   <image class="img" :src="content.avatar"></image>
			   </vrow>
			   <vrow style="border-bottom:1px solid #ECECEC;padding:5upx 0;" class="font28">
				   <vcol span="50" class="white" style="text-align:left;"><uni-icons style="color:#fff; margin-left: 10upx;" type="person" size="17"></uni-icons><text>{{content.member_name}}</text></vcol>
				   <vcol span="50" class="white" style="text-align:right;"><uni-icons style="color:#fff;" type="phone" size="18"></uni-icons><text style="margin-right: 10upx;">{{content.phone}}</text></vcol>
			   </vrow>
			   <vrow style="border-bottom:1px solid #ECECEC;color:#F7A200; padding: 10upx 0;" class="font28">
				   <text style="margin: 10upx 20upx;">作品及奖项 : {{content.production}}</text>
			   </vrow>
		    </vcol>	
		</vrow>
		<vrow>
			<vcol span="100">
				<view class="contentcss"><u-parse :content="content.description" userSelect="none" /></view>				
			</vcol>	
		</vrow>
	</view>
</template>

<script>
	import vrow from '@/components/lml-layout/row.vue'
	import vcol from '@/components/lml-layout/col.vue'
	import uParse from '@/components/gaoyia-parse/parse.vue'
	import uniIcons from "@/components/uni-icons/uni-icons.vue"


		
	export default {
		components:{
			vrow,vcol,uParse,uniIcons
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
	        	url:self.$url+'index.php/index/Index/designerdetail',
				data:{id:options.designerid},
	        	method:'POST',
				header:{'content-type':'application/x-www-form-urlencoded'},
	        	success:function(res){
					res.data.avatar=self.$url+res.data.avatar.replace(/\\/g, "/");//拼接处理图片路径
	        		self.content=res.data;
					console.log(res)
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
		background:linear-gradient(top,#F5E100,#1DA261);/*chrom*/
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
		width:180upx;
		height:180upx;
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
