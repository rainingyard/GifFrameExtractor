<?php
class UploadController{
	/**
	*This is a demo about resize GIF.
	*
	*CODE LOGIC
	*
	*If size<1MB:
 	*Get the longer edge's length
    *  If >1024px，resize to 1024px
    *  Else if <=1024px，no action.
	*Else if size >=1MB
	*Get the longer edge's length
    *  If >300，resize to 300px
    *  Else if<=300px，no action	
	**/
	public function addbatchAction() {

		//upload gif
		$data = array();
		$relfile = \Lib\File::upload('');
		// one is resized gif; one is origin gif
		$data['file'] = $relfile;
		$data['origin'] = $relfile;
		$pic_info = getimagesize(PUBLIC_PATH . $data['file']);
		list($width, $height) = $pic_info;
		$origin_size = filesize(PUBLIC_PATH . $data['file']);
		if ($origin_size > 1000000) {
			$src_type = strtolower(substr(strrchr($pic_info['mime'], "/"), 1));
			$limit = 300;
			if ($width > $limit || $height > $limit) {
				if ($width > $height) {
					$dst_width = $limit;
					$dst_height = intval(($height / $width) * $dst_width);
				} else {
					$dst_height = $limit;
					$dst_width = intval(($width / $height) * $dst_height);
				}
			} else {
				$this->jsonp($data, TRUE, 0, 'Upload successfully', 'Callback');
				return;
			}
			//all gif convert to 300*xxx
			if ($src_type == 'gif') {
				if (\Lib\GifFrameExtractor::isAnimatedGif(PUBLIC_PATH . $data['file'])) {
					$gfe = new \Lib\GifFrameExtractor();
					$gfe->extract(PUBLIC_PATH . $data['file']);
					$frameImages = $gfe->getFrameImages();
					$frameDurations = $gfe->getFrameDurations();
					foreach ($frameImages as $src_frame) {
						$dst_frame = imagecreatetruecolor($dst_width, $dst_height);
						imagecopyresized($dst_frame, $src_frame, 0, 0, 0, 0, $dst_width, $dst_height, $width, $height);
						$resources[] = $dst_frame;
					}
					$gc = new \Lib\GifCreator();
					$gc->create($resources, $frameDurations, 0); //0 meaning infinite loop
					$gifBinary = $gc->getGif();
					$dst_path = substr($data['file'], 0, stripos($data['file'], '_') + 1) . $dst_width . '*' . $dst_height . '.gif';
					file_put_contents(PUBLIC_PATH . $dst_path, $gifBinary);
					$data['origin'] = $data['file'];
					$data['file'] = $dst_path;

				} else {
					$dst_path = substr($data['file'], 0, stripos($data['file'], '_') + 1) . $dst_width . '*' . $dst_height . '.gif';
					$src_frame = imagecreatefromgif(PUBLIC_PATH . $data['file']);
					$dst_frame = imagecreatetruecolor($dst_width, $dst_height);
					imagecopyresized($dst_frame, $src_frame, 0, 0, 0, 0, $dst_width, $dst_height, $width, $height);
					file_put_contents(PUBLIC_PATH . $dst_path, $dst_frame);
					$data['origin'] = $data['file'];
					$data['file'] = $dst_path;
					imagedestroy($dst_frame);
					imagedestroy($src_frame);
				}
			}
		} elseif($origin_size <= 1000000) {
			$limit = 1024;
			if ($width > $limit || $height > $limit) {
				if ($width > $height) {
					$dst_width = $limit;
					$dst_height = intval(($height / $width) * $dst_width);
				} else {
					$dst_height = $limit;
					$dst_width = intval(($width / $height) * $dst_height);
				}

				//all gif convert to 1024*xxx
				if ($src_type == 'gif') {
					if (\Lib\GifFrameExtractor::isAnimatedGif(PUBLIC_PATH . $data['file'])) {
						$gfe = new \Lib\GifFrameExtractor();
						$gfe->extract(PUBLIC_PATH . $data['file']);
						$frameImages = $gfe->getFrameImages();
						$frameDurations = $gfe->getFrameDurations();
						foreach ($frameImages as $src_frame) {
							$dst_frame = imagecreatetruecolor($dst_width, $dst_height);
							imagecopyresized($dst_frame, $src_frame, 0, 0, 0, 0, $dst_width, $dst_height, $width, $height);
							$resources[] = $dst_frame;
						}
						$gc = new \Lib\GifCreator();
						$gc->create($resources, $frameDurations, 0); //0 meaning infinite loop
						$gifBinary = $gc->getGif();
						$dst_path = substr($data['file'], 0, stripos($data['file'], '_') + 1) . $dst_width . '*' . $dst_height . '.gif';
						file_put_contents(PUBLIC_PATH . $dst_path, $gifBinary);
						$data['origin'] = $data['file'];
						$data['file'] = $dst_path;

					} else {
						$dst_path = substr($data['file'], 0, stripos($data['file'], '_') + 1) . $dst_width . '*' . $dst_height . '.gif';
						$src_frame = imagecreatefromgif(PUBLIC_PATH . $data['file']);
						$dst_frame = imagecreatetruecolor($dst_width, $dst_height);
						imagecopyresized($dst_frame, $src_frame, 0, 0, 0, 0, $dst_width, $dst_height, $width, $height);
						file_put_contents(PUBLIC_PATH . $dst_path, $dst_frame);
						$data['origin'] = $data['file'];
						$data['file'] = $dst_path;
						imagedestroy($dst_frame);
						imagedestroy($src_frame);
					}
				}

			} else {
				$this->jsonp($data, TRUE, 0, 'Upload successfully', 'Callback');
				return;
			}
		}

		$this->jsonp($data, TRUE, 0, 'Upload successfully', 'Callback');
}