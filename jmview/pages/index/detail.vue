<template>
	<view class="content">
		<vrow class="borbottom">
		    <vcol span="35" class="center fon25">
               <vrow>
				   <image class="img" :src="content.avatar"></image>
			   </vrow>
			   <vrow class="designname">设计师：{{content.member_name}}</vrow>
		    </vcol>	
		    <vcol span="65" class="center fon25 colheight">
                <vrow class="colheight-vrow">
					<vcol span="21">风格</vcol>
					<vcol span="21">大小</vcol>
					<vcol span="21">造价</vcol>
				</vrow>
				<vrow>
					<vcol span="21">{{content.keywords}}</vcol>
					<vcol span="21">{{content.area}}</vcol>
					<vcol span="21">{{content.price}}</vcol>
				</vrow>
		    </vcol>	
		</vrow>
	</view>
</template>

<script>
	import vrow from '@/components/lml-layout/row.vue'
	import vcol from '@/components/lml-layout/col.vue'

		
	export default {
		components:{
			vrow,vcol
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
	        	url:'/dpc/index/Index/detail',
				data:{id:options.titleid},
	        	method:'POST',
				header:{'content-type':'application/x-www-form-urlencoded'},
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
		background:linear-gradient(left,#82858A,#695648);/*chrom*/
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
	.mp0{
		margin: 0;
		padding: 0;
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
</style>
